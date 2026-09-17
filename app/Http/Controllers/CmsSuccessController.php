<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CmsSuccessController extends Controller
{
    public const NETWORKS = ['facebook' => 'Facebook', 'x' => 'X', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'instagram' => 'Instagram', 'tiktok' => 'TikTok'];

    private function section(): ?CmsSection
    {
        return CmsSection::whereHas('page', fn ($query) => $query->where('key', 'index'))->where('key', 'temoignages')->first();
    }

    private function settings()
    {
        return DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key');
    }

    public function show()
    {
        $section = $this->section();

        return view('adpdh.success', [
            'testimonials' => $section?->contents()->with('media')->where('is_visible', true)->where('is_demo', false)->get() ?? collect(),
            'titles' => CmsPage::where('key', 'temoignages')->first()?->titles()->pluck('value', 'key') ?? collect(),
            'settings' => $this->settings(),
            'networks' => self::NETWORKS,
            'footerSections' => CmsPage::where('key', 'index')->first()?->sections()->with('contents')->get() ?? collect(),
        ]);
    }

    public function edit()
    {
        return view('cms.success.index', ['section' => $this->section()?->load('contents'), 'page' => CmsPage::where('key', 'temoignages')->first(), 'settings' => $this->settings(), 'networks' => self::NETWORKS]);
    }

    public function save(Request $request)
    {
        $rules = ['introduction' => 'required|string|max:1000'];
        foreach (self::NETWORKS as $key => $label) {
            $rules['social.'.$key.'.enabled'] = 'required|boolean';
            $rules['social.'.$key.'.url'] = ['nullable', 'required_if:social.'.$key.'.enabled,1', 'url:http,https', 'max:1000'];
        }
        $data = $request->validate($rules, ['required_if' => 'Renseignez l’adresse du réseau avant de l’activer.', 'url' => 'Saisissez une adresse complète commençant par https:// ou http://.']);
        DB::transaction(function () use ($data) {
            $values = ['adpdh.success.introduction' => $data['introduction']];
            foreach (self::NETWORKS as $key => $label) {
                $values['adpdh.success.social.'.$key.'.enabled'] = $data['social'][$key]['enabled'] ? '1' : '0';
                $values['adpdh.success.social.'.$key.'.url'] = $data['social'][$key]['url'] ?? '';
            }
            foreach ($values as $key => $value) {
                DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value, 'updated_at' => now()]);
            }
        });

        return back()->with('status', 'Présentation et réseaux sociaux enregistrés.');
    }
}
