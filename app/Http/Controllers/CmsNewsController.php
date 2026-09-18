<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\MediaAsset;
use App\Models\Post;
use App\Support\ActivityHtml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsNewsController extends Controller
{
    public static function revision(Post $post): string
    {
        return hash('sha256', json_encode($post->getAttributes()));
    }

    private function context(): array
    {
        return [
            'settings' => DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key'),
            'footerSections' => CmsPage::where('key', 'index')->first()?->sections()->with('contents')->get() ?? collect(),
        ];
    }

    public function index()
    {
        return view('adpdh.news', $this->context() + ['news' => Post::publiclyVisible()->with('cover')->latest('published_at')->latest('id')->paginate(6)]);
    }

    public function show(string $slug)
    {
        $post = Post::publiclyVisible()->with('cover')->where('slug', $slug)->firstOrFail();

        return view('adpdh.news-article', $this->context() + compact('post'));
    }

    public function manage()
    {
        return view('cms.news.index', ['posts' => Post::forCms()->latest('id')->paginate(20)]);
    }

    public function edit(?Post $post = null)
    {
        abort_if($post && ! $post->cms_key, 404);

        return view('cms.news.edit', ['post' => $post ?? new Post(['status' => 'draft', 'published_at' => now()])]);
    }

    public function save(Request $request, ?Post $post = null)
    {
        abort_if($post && ! $post->cms_key, 404);
        $data = $request->validate([
            'revision' => $post ? 'required|string' : 'nullable|string',
            'title' => 'required|string|max:255', 'excerpt' => 'required|string|max:2000',
            'content' => 'required|string|max:100000', 'category' => 'required|string|max:255',
            'status' => 'required|in:draft,published', 'published_at' => 'required|date',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'remove_cover' => 'nullable|boolean',
        ]);
        $content = ActivityHtml::clean($data['content']);
        if (! trim(html_entity_decode(strip_tags($content)))) {
            throw ValidationException::withMessages(['content' => 'Rédigez le contenu de l’actualité.']);
        }
        $path = null;
        try {
            $record = DB::transaction(function () use ($request, $post, $data, $content, &$path) {
                $record = $post ? Post::lockForUpdate()->findOrFail($post->id) : new Post([
                    'cms_key' => 'actualite-'.Str::uuid(), 'slug' => (Str::slug($data['title']) ?: 'actualite').'-'.Str::lower(Str::random(8)),
                    'user_id' => $request->user()->id, 'is_demo' => false,
                ]);
                if ($post && ! hash_equals(self::revision($record), $data['revision'])) {
                    throw ValidationException::withMessages(['revision' => 'Cette actualité a changé. Rechargez sa fiche avant de réessayer.']);
                }
                $record->fill(collect($data)->only(['title', 'excerpt', 'category', 'status', 'published_at'])->all());
                $record->content = $content;
                if ($request->boolean('remove_cover')) {
                    $record->cover_media_id = null;
                }
                if ($request->hasFile('cover')) {
                    $file = $request->file('cover');
                    $path = $file->store('news', 'public');
                    $record->cover_media_id = MediaAsset::create([
                        'key' => 'news-'.Str::uuid(), 'name' => $file->getClientOriginalName(), 'kind' => 'image',
                        'disk' => 'public', 'path' => $path, 'visibility' => 'public', 'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(), 'alt' => $record->title, 'publication_allowed' => true,
                        'is_demo' => false, 'uploaded_by' => $request->user()->id,
                    ])->id;
                }
                $record->save();

                return $record;
            });
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            throw $exception;
        }

        return redirect()->route('admin.cms.news.edit', $record)->with('status', 'Actualité enregistrée.');
    }
}
