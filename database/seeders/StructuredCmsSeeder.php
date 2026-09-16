<?php

namespace Database\Seeders;

use App\Models\CmsLegacyMapping;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\ContentCategory;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HistoryEvent;
use App\Models\Indicator;
use App\Models\InterventionAxis;
use App\Models\InterventionZone;
use App\Models\MediaAsset;
use App\Models\MediaFolder;
use App\Models\OrganizationValue;
use App\Models\PartnershipType;
use App\Models\Pillar;
use App\Models\Project;
use App\Models\Publication;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StructuredCmsSeeder extends Seeder
{
    private array $legacy = [];

    private function resolveParts(string $page, array $parts): string
    {
        return trim(implode('', array_map(fn ($p) => isset($p['legacy']) ? ($p['prefix'] ?? '').($this->legacy[$page][$p['legacy']]['value'] ?? '').($p['suffix'] ?? '') : ($p['literal'] ?? ''), $parts)));
    }

    private function mapped($model, string $page, array $fields): void
    {
        if (! $model->wasRecentlyCreated) {
            return;
        }
        foreach ($fields as $field => $parts) {
            foreach ($parts as $part) {
                if (isset($part['legacy'], $this->legacy[$page][$part['legacy']])) {
                    $legacy = $this->legacy[$page][$part['legacy']];
                    CmsLegacyMapping::firstOrCreate(['cms_title_id' => $legacy['id']], ['target_type' => $model->getMorphClass(), 'target_id' => $model->id, 'target_field' => $field, 'imported_value' => $legacy['value']]);
                }
            }
        }
    }

    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('content/adpdh-structured.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($data) {
            foreach (CmsPage::with('titles')->get() as $p) {
                $this->legacy[$p->key] = $p->titles->keyBy('key')->map(fn ($t) => ['id' => $t->id, 'value' => $t->value])->all();
            }
            foreach (CmsPage::all() as $p) {
                if ($p->template !== null) {
                    continue;
                }
                $kind = str_starts_with($p->key, 'activite-') || in_array($p->key, ['actualite', 'actualite-cycle-avec', 'temoignage-exemple']) ? 'record' : 'institutional';
                $p->update(['template' => $p->key, 'page_kind' => $kind, 'seo_title' => $this->legacy[$p->key]['text-1']['value'], 'seo_description' => $this->legacy[$p->key]['text-2']['value']]);
            }
            $folder = MediaFolder::firstOrCreate(['key' => 'identite-et-illustrations'], ['name' => 'Identité et illustrations initiales']);
            $media = [];
            foreach ($data['media'] as $entry) {
                $variants = $entry['variants'];
                unset($entry['variants']);
                $media[$entry['key']] = MediaAsset::firstOrCreate(['key' => $entry['key']], $entry + ['media_folder_id' => $folder->id, 'kind' => 'image', 'disk' => 'builtin', 'visibility' => 'public', 'publication_allowed' => true, 'alt' => $entry['is_illustration'] ? 'Illustration — ne représente pas un événement réel ADPDH' : null]);
                foreach ($variants as $v) {
                    $media[$entry['key']]->variants()->firstOrCreate(['name' => $v['name']], $v);
                }
            }
            foreach ($data['sections'] as $order => $entry) {
                $p = CmsPage::where('key', $entry['page'])->firstOrFail();
                $fields = [];
                foreach ($entry['fields'] as $key => $parts) {
                    $fields[$key] = $this->resolveParts($entry['page'], $parts);
                }
                $section = CmsSection::firstOrCreate(['cms_page_id' => $p->id, 'key' => $entry['key']], $fields + ['label' => $entry['label'], 'template' => $entry['template'], 'collection' => $entry['collection'], 'body' => $entry['body'], 'buttons' => $entry['buttons'], 'sort_order' => $order]);
                $this->mapped($section, $entry['page'], $entry['fields']);
            }
            foreach ($data['pillars'] as $i => $entry) {
                $pillar = Pillar::withTrashed()->firstOrCreate(['key' => $entry['key']], ['title' => $this->resolveParts($entry['page'], $entry['title_parts']), 'description' => $entry['description'], 'sort_order' => $i + 1]);
                $this->mapped($pillar, $entry['page'], ['title' => $entry['title_parts']]);
                foreach ($entry['axes'] as $j => $axis) {
                    $model = InterventionAxis::withTrashed()->firstOrCreate(['key' => $axis['key']], ['pillar_id' => $pillar->id, 'title' => $this->resolveParts($entry['page'], $axis['title_parts']), 'description' => $axis['description'], 'sort_order' => (int) str_replace('axe-', '', $axis['key'])]);
                    $this->mapped($model, $entry['page'], ['title' => $axis['title_parts']]);
                }
            }
            $activities = [];
            foreach ($data['activities'] as $i => $entry) {
                $category = ContentCategory::firstOrCreate(['kind' => 'activity', 'slug' => $entry['key']], ['name' => ['Autonomisation économique', 'Leadership et cohésion sociale', 'Réintégration socio-économique'][$i], 'sort_order' => $i]);
                $gallery = Gallery::firstOrCreate(['key' => 'activite-'.$entry['key']], ['title' => 'Galerie — '.$entry['key'], 'description' => 'Illustrations provisoires, à remplacer par des photos autorisées.']);
                foreach (['community', 'workshop'] as $j => $key) {
                    $gallery->items()->firstOrCreate(['media_asset_id' => $media[$key]->id], ['sort_order' => $j, 'caption' => $media[$key]->credit]);
                }
                $fields = collect($entry)->only(['description', 'objective', 'audience', 'location', 'activity_status', 'period_label', 'start_year', 'end_year', 'results_note', 'source_note'])->all();
                $activity = Project::firstOrCreate(['cms_key' => $entry['key']], $fields + ['slug' => 'adpdh-'.$entry['key'], 'title' => $this->resolveParts($entry['page'], $entry['title_parts']), 'category' => $category->name, 'content_category_id' => $category->id, 'publication_state' => 'published', 'cover_media_id' => $media[$entry['cover']]->id, 'gallery_id' => $gallery->id, 'sort_order' => $i]);
                $activities[$entry['key']] = $activity;
                $this->mapped($activity, $entry['page'], ['title' => $entry['title_parts']]);
                foreach ($entry['steps'] as $j => $step) {
                    $model = $activity->steps()->firstOrCreate(['key' => $step['key']], ['title' => $this->resolveParts($entry['page'], $step['title_parts']), 'description' => $step['description'], 'sort_order' => $j]);
                    $this->mapped($model, $entry['page'], ['title' => $step['title_parts']]);
                }
                if ($activity->wasRecentlyCreated) {
                    $activity->axes()->sync(InterventionAxis::whereIn('key', $entry['axes'])->pluck('id'));
                }
            }
            $members = json_decode(file_get_contents(base_path('public/adpdh/data/team.json')), true, 512, JSON_THROW_ON_ERROR);
            foreach ($members as $i => $entry) {
                TeamMember::firstOrCreate(['cms_key' => Str::slug($entry['name'])], ['name' => $entry['name'], 'position' => $entry['role'], 'phone' => $entry['phone'], 'email' => $entry['email'], 'photo' => null, 'publication_state' => 'published', 'sort_order' => $i]);
            }
            foreach (['history' => HistoryEvent::class, 'values' => OrganizationValue::class] as $key => $class) {
                foreach ($data[$key] as $i => $entry) {
                    $fields = ['title' => $this->resolveParts($entry['page'], $entry['title_parts']), 'description' => $entry['description'], 'sort_order' => $i];
                    if (isset($entry['period_label'])) {
                        $fields['period_label'] = $entry['period_label'];
                    }
                    $model = $class::firstOrCreate(['key' => $entry['key']], $fields);
                    $this->mapped($model, $entry['page'], ['title' => $entry['title_parts']]);
                }
            }
            foreach ($data['zones'] as $i => $entry) {
                InterventionZone::firstOrCreate(['key' => $entry['key']], $entry + ['sort_order' => $i]);
            }
            $indicators = [
                ['step-beneficiaires', 'Bénéficiaires THIMO / STEP', 1400, 'count', 'thimo-step', '2024', null, 'Kamituga, Bukavu et Uvira'],
                ['avec-creees', 'AVEC créées', 3, 'count', 'avec-agr', null, null, 'AVEC accompagnées par ADPDH'],
                ['avec-membres', 'Membres actifs des AVEC', 120, 'count', 'avec-agr', null, null, 'AVEC accompagnées par ADPDH'],
                ['avec-remboursement', 'Remboursement des crédits', 90, 'percent', 'avec-agr', null, null, 'Crédits des AVEC accompagnées'],
                ['agr-gestion', 'Membres appliquant la séparation des caisses', 113, 'count', 'avec-agr', null, null, 'Membres formés à la gestion des AGR'],
                ['agr-actives', 'AGR toujours actives', 56, 'count', 'avec-agr', null, 6, 'Activités six mois après la formation'],
            ];
            foreach ($indicators as $i => [$key, $title, $value, $unit, $activity, $period, $months, $scope]) {
                $indicator = Indicator::firstOrCreate(['key' => $key], ['title' => $title, 'unit' => $unit, 'project_id' => $activities[$activity]->id, 'sort_order' => $i]);
                if (! $indicator->values()->exists()) {
                    $indicator->values()->create(['value' => $value, 'period_label' => $period, 'followup_months' => $months, 'scope' => $scope, 'source' => 'Contenu.Site.Web.ADPDH.pdf — Notre impact', 'limitations' => $period === null ? 'Date de référence et méthode de collecte à préciser.' : 'Source institutionnelle ; rapprochement avec les rapports STEP à effectuer.', 'change_note' => 'Donnée initiale du document institutionnel']);
                }
            }
            foreach ($data['partnerships'] as $i => $entry) {
                $model = PartnershipType::firstOrCreate(['key' => $entry['key']], ['title' => $this->resolveParts($entry['page'], $entry['title_parts']), 'description' => $entry['description'], 'sort_order' => $i]);
                $this->mapped($model, $entry['page'], ['title' => $entry['title_parts']]);
            }
            foreach ($data['publications'] as $i => $entry) {
                $category = ContentCategory::firstOrCreate(['kind' => 'resource', 'slug' => $entry['category']], ['name' => ucfirst($entry['category'])]);
                $model = Publication::firstOrCreate(['cms_key' => $entry['key']], ['title' => $this->resolveParts($entry['page'], $entry['title_parts']), 'slug' => $entry['key'], 'description' => $entry['description'], 'category' => $category->name, 'content_category_id' => $category->id, 'publication_state' => 'published', 'availability' => 'pending', 'sort_order' => $i]);
                $this->mapped($model, $entry['page'], ['title' => $entry['title_parts']]);
            }
            foreach ($data['faq'] as $i => $entry) {
                Faq::firstOrCreate(['cms_key' => $entry['key']], ['question' => $entry['question'], 'answer' => $entry['answer'], 'context' => 'don', 'order' => $i]);
            }
            $settings = ['identity.name' => 'ADPDH', 'identity.full_name' => 'Action pour le Développement et la Promotion des Droits Humains', 'contact.email' => 'contact@adpdh.org', 'contact.phone' => '+243 896 263 558', 'contact.address' => '305, avenue Patrice Emery Lumumba, commune d’Ibanda, Bukavu, Sud-Kivu, RDC', 'donation.bank' => 'Equity BCDC', 'donation.account_name' => 'Action pour Le Developpement Et La Promotions Des Droits Humains', 'donation.account_number' => '400200086445762', 'legal.registration' => null, 'legal.publisher' => null, 'legal.host' => null];
            foreach ($settings as $key => $value) {
                DB::table('settings')->insertOrIgnore(['key' => 'adpdh.'.$key, 'value' => $value, 'group' => explode('.', $key)[0], 'value_type' => 'text', 'created_at' => now(), 'updated_at' => now()]);
            }
        });
    }
}
