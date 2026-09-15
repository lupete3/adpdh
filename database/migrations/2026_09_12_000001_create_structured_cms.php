<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->string('name');
            $t->timestamps();
        });
        Schema::create('media_assets', function (Blueprint $t) {
            $t->id();
            $t->string('key')->nullable()->unique();
            $t->foreignId('media_folder_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('kind', 20);
            $t->string('disk', 30)->default('local');
            $t->string('path');
            $t->string('visibility', 20)->default('private');
            $t->string('mime_type');
            $t->unsignedBigInteger('size')->default(0);
            $t->unsignedInteger('width')->nullable();
            $t->unsignedInteger('height')->nullable();
            $t->text('alt')->nullable();
            $t->text('caption')->nullable();
            $t->text('credit')->nullable();
            $t->text('source')->nullable();
            $t->boolean('is_illustration')->default(false);
            $t->boolean('is_demo')->default(false);
            $t->boolean('publication_allowed')->default(false);
            $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->unique(['disk', 'path']);
        });
        Schema::create('media_variants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('media_asset_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('path');
            $t->string('mime_type');
            $t->unsignedInteger('width');
            $t->unsignedInteger('height');
            $t->unsignedBigInteger('size');
            $t->unique(['media_asset_id', 'name']);
        });
        Schema::create('galleries', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->string('title');
            $t->text('description')->nullable();
            $t->timestamps();
        });
        Schema::create('gallery_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('gallery_id')->constrained()->cascadeOnDelete();
            $t->foreignId('media_asset_id')->constrained()->restrictOnDelete();
            $t->text('caption')->nullable();
            $t->text('alt')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
            $t->unique(['gallery_id', 'media_asset_id']);
        });
        Schema::create('content_categories', function (Blueprint $t) {
            $t->id();
            $t->string('kind', 30);
            $t->string('slug');
            $t->string('name');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
            $t->unique(['kind', 'slug']);
        });
        Schema::table('cms_pages', function (Blueprint $t) {
            $t->string('template')->nullable();
            $t->string('page_kind', 30)->default('institutional');
            $t->string('seo_title')->nullable();
            $t->text('seo_description')->nullable();
        });
        Schema::create('cms_sections', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cms_page_id')->constrained()->cascadeOnDelete();
            $t->string('key');
            $t->string('label');
            $t->string('template');
            $t->string('eyebrow')->nullable();
            $t->text('title')->nullable();
            $t->text('title_accent')->nullable();
            $t->text('introduction')->nullable();
            $t->json('body')->nullable();
            $t->foreignId('media_asset_id')->nullable()->constrained()->nullOnDelete();
            $t->string('collection', 40)->nullable();
            $t->json('buttons')->nullable();
            $t->boolean('is_visible')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->unsignedInteger('version')->default(1);
            $t->timestamps();
            $t->unique(['cms_page_id', 'key']);
        });
        Schema::create('pillars', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->string('title');
            $t->text('description');
            $t->unsignedInteger('sort_order');
            $t->timestamps();
        });
        Schema::create('intervention_axes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pillar_id')->constrained()->restrictOnDelete();
            $t->string('key')->unique();
            $t->string('title');
            $t->text('description');
            $t->unsignedInteger('sort_order');
            $t->timestamps();
        });
        Schema::table('projects', function (Blueprint $t) {
            $t->string('cms_key')->nullable()->unique();
            $t->string('activity_status', 30)->default('ongoing');
            $t->string('publication_state', 20)->default('draft');
            $t->boolean('is_demo')->default(false);
            $t->text('objective')->nullable();
            $t->text('audience')->nullable();
            $t->text('location')->nullable();
            $t->string('period_label')->nullable();
            $t->unsignedSmallInteger('start_year')->nullable();
            $t->unsignedSmallInteger('end_year')->nullable();
            $t->text('results_note')->nullable();
            $t->text('source_note')->nullable();
            $t->json('body')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->foreignId('cover_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $t->foreignId('gallery_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('content_category_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::create('activity_steps', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->string('key');
            $t->string('title');
            $t->text('description');
            $t->unsignedInteger('sort_order');
            $t->unique(['project_id', 'key']);
        });
        Schema::create('activity_axis', function (Blueprint $t) {
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->foreignId('intervention_axis_id')->constrained()->restrictOnDelete();
            $t->primary(['project_id', 'intervention_axis_id']);
        });
        Schema::table('team_members', function (Blueprint $t) {
            $t->string('photo')->nullable()->change();
            $t->string('cms_key')->nullable()->unique();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->boolean('show_contacts')->default(true);
            $t->string('publication_state', 20)->default('draft');
            $t->unsignedInteger('sort_order')->default(0);
            $t->foreignId('photo_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
        });
        foreach (['history_events', 'organization_values', 'intervention_zones'] as $table) {
            Schema::create($table, function (Blueprint $t) use ($table) {
                $t->id();
                $t->string('key')->unique();
                $t->string('title');
                $t->text('description');
                $t->unsignedInteger('sort_order')->default(0);
                $t->boolean('is_visible')->default(true);
                if ($table === 'history_events') {
                    $t->string('period_label');
                }
                if ($table === 'intervention_zones') {
                    $t->string('zone_status', 20)->default('current');
                }
                $t->timestamps();
            });
        }
        Schema::create('indicators', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->string('title');
            $t->string('unit', 20)->default('count');
            $t->text('description')->nullable();
            $t->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_visible')->default(true);
            $t->timestamps();
        });
        Schema::create('indicator_values', function (Blueprint $t) {
            $t->id();
            $t->foreignId('indicator_id')->constrained()->cascadeOnDelete();
            $t->decimal('value', 20, 4);
            $t->string('period_label')->nullable();
            $t->unsignedSmallInteger('followup_months')->nullable();
            $t->text('scope')->nullable();
            $t->text('source');
            $t->text('method')->nullable();
            $t->text('limitations')->nullable();
            $t->text('change_note')->nullable();
            $t->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
        Schema::table('posts', function (Blueprint $t) {
            $t->string('cms_key')->nullable()->unique();
            $t->string('slug')->nullable()->unique();
            $t->text('excerpt')->nullable();
            $t->json('body')->nullable();
            $t->boolean('is_demo')->default(false);
            $t->timestamp('published_at')->nullable();
            $t->foreignId('cover_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $t->foreignId('content_category_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::table('testimonials', function (Blueprint $t) {
            $t->string('author_photo')->nullable()->change();
            $t->string('author_position')->nullable()->change();
            $t->string('cms_key')->nullable()->unique();
            $t->string('slug')->nullable()->unique();
            $t->string('title')->nullable();
            $t->string('kind', 20)->default('testimony');
            $t->string('publication_state', 20)->default('draft');
            $t->boolean('is_demo')->default(false);
            $t->json('body')->nullable();
            $t->timestamp('consent_at')->nullable();
            $t->foreignId('consent_media_id')->nullable()->constrained('media_assets')->restrictOnDelete();
            $t->foreignId('photo_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $t->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('gallery_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedInteger('sort_order')->default(0);
        });
        Schema::table('publications', function (Blueprint $t) {
            $t->string('cms_key')->nullable()->unique();
            $t->string('slug')->nullable()->unique();
            $t->string('publication_state', 20)->default('draft');
            $t->boolean('is_demo')->default(false);
            $t->boolean('distribution_allowed')->default(false);
            $t->string('availability', 20)->default('pending');
            $t->foreignId('file_media_id')->nullable()->constrained('media_assets')->restrictOnDelete();
            $t->foreignId('cover_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $t->foreignId('content_category_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedInteger('sort_order')->default(0);
        });
        Schema::create('partnership_types', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->string('title');
            $t->text('description');
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_visible')->default(true);
            $t->timestamps();
        });
        Schema::table('faqs', function (Blueprint $t) {
            $t->string('cms_key')->nullable()->unique();
            $t->string('context')->nullable();
            $t->boolean('is_visible')->default(true);
        });
        Schema::table('settings', function (Blueprint $t) {
            $t->string('group')->nullable();
            $t->string('value_type', 20)->default('text');
        });
        Schema::table('contact_messages', function (Blueprint $t) {
            $t->string('kind', 20)->default('contact');
            $t->string('status', 20)->default('new');
            $t->timestamp('handled_at')->nullable();
        });
        Schema::create('content_revisions', function (Blueprint $t) {
            $t->id();
            $t->morphs('revisable');
            $t->json('snapshot');
            $t->string('reason')->nullable();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
        });
        Schema::create('cms_legacy_mappings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cms_title_id')->constrained()->cascadeOnDelete();
            $t->string('target_type');
            $t->unsignedBigInteger('target_id');
            $t->string('target_field');
            $t->text('imported_value');
            $t->timestamps();
            $t->unique('cms_title_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_legacy_mappings');
        Schema::dropIfExists('content_revisions');
        Schema::table('contact_messages', fn (Blueprint $t) => $t->dropColumn(['kind', 'status', 'handled_at']));
        Schema::table('settings', fn (Blueprint $t) => $t->dropColumn(['group', 'value_type']));
        Schema::table('faqs', fn (Blueprint $t) => $t->dropColumn(['cms_key', 'context', 'is_visible']));
        Schema::dropIfExists('partnership_types');
        $foreign = ['publications' => ['file_media_id', 'cover_media_id', 'content_category_id'], 'testimonials' => ['consent_media_id', 'photo_media_id', 'project_id', 'gallery_id'], 'posts' => ['cover_media_id', 'content_category_id'], 'team_members' => ['photo_media_id'], 'projects' => ['cover_media_id', 'gallery_id', 'content_category_id']];
        $columns = ['publications' => ['cms_key', 'slug', 'publication_state', 'is_demo', 'distribution_allowed', 'availability', 'sort_order'], 'testimonials' => ['cms_key', 'slug', 'title', 'kind', 'publication_state', 'is_demo', 'body', 'consent_at', 'sort_order'], 'posts' => ['cms_key', 'slug', 'excerpt', 'body', 'is_demo', 'published_at'], 'team_members' => ['cms_key', 'phone', 'email', 'show_contacts', 'publication_state', 'sort_order'], 'projects' => ['cms_key', 'activity_status', 'publication_state', 'is_demo', 'objective', 'audience', 'location', 'period_label', 'start_year', 'end_year', 'results_note', 'source_note', 'body', 'sort_order']];
        Schema::dropIfExists('indicator_values');
        Schema::dropIfExists('indicators');
        Schema::dropIfExists('activity_axis');
        Schema::dropIfExists('activity_steps');
        foreach ($foreign as $table => $keys) {
            Schema::table($table, function (Blueprint $t) use ($keys, $columns, $table) {
                foreach ($keys as $key) {
                    $t->dropConstrainedForeignId($key);
                }
                $t->dropColumn($columns[$table]);
            });
        }
        // Nullable legacy photos remain nullable: rolling back must not fabricate photos for new members.
        foreach (['intervention_zones', 'organization_values', 'history_events', 'intervention_axes', 'pillars', 'cms_sections', 'content_categories', 'gallery_items', 'galleries', 'media_variants', 'media_assets', 'media_folders'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('cms_pages', fn (Blueprint $t) => $t->dropColumn(['template', 'page_kind', 'seo_title', 'seo_description']));
    }
};
