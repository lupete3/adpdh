<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\About;
use App\Models\Achievement;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Service;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Feature;
use App\Models\WhyUs;
use App\Models\Cta;
use App\Models\SkillHeader;
use App\Models\ServiceHeader;
use App\Models\SectionHeader;
use App\Models\Skill;
use Illuminate\Support\Str;

class FlexBizSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Sliders (Hero)
        Slider::query()->delete();
        Slider::create([
            'title' => 'WEZESHA FOUNDATION',
            'subtitle' => 'TRANSFORMING THE FUTURE OF DRC',
            'description' => 'Promouvoir une éducation de qualité et le bien-être social des orphelins et familles défavorisées en RDC.',
            'image' => 'flexbiz/assets/img/illustration/illustration-8.webp',
            'secondary_image' => 'flexbiz/assets/img/illustration/illustration-15.webp',
            'floating_badge' => '10+ Ans d\'Impact',
            'button1_text' => 'En savoir plus',
            'button1_url' => '#about',
            'button2_text' => 'Faire un don',
            'button2_url' => '#',
            'mini_stats' => [
                ['icon' => 'bi bi-mortarboard', 'label' => 'Éducation'],
                ['icon' => 'bi bi-heart', 'label' => 'Humanitaire'],
                ['icon' => 'bi bi-shield-check', 'label' => 'Impact']
            ],
            'order' => 1,
        ]);

        // 2. About
        About::create([
            'title' => 'Une génération d’enfants orphelins éduqués, autonomes et résiliants',
            'subtitle' => 'Qui sommes-nous ?',
            'content' => "WEZESHA FOUNDATION est dédiée à la promotion d’une éducation de qualité et équitable pour tous les enfants orphelins, ainsi qu’au bien-être social des familles économiquement défavorisées en RDC. Nous œuvrons pour transformer durablement la société à travers l'autonomisation et le développement communautaire.",
            'image' => 'flexbiz/assets/img/about.jpg',
            'kicker' => 'Notre Vision',
            'badge_title' => '10+ ans',
            'badge_text' => 'D\'impact social',
            'metrics' => [
                ['value' => '120+', 'label' => 'Orphelins'],
                ['value' => '10k+', 'label' => 'Familles'],
                ['value' => '95%', 'label' => 'Réussite']
            ],
            'button_text' => 'Notre Histoire',
            'button_url' => '#',
            'video_url' => '#',
            'features' => [
                'Vision: Une génération d’enfants orphelins éduqués, autonomes et enracinés dans des communautés résiliantes.',
                'Mission: Fournir diverses formes de soutien aux familles défavorisées pour les aider à se développer.',
                'Valeur: Transparence et Redevabilité dans toutes nos actions.',
                'Engagement: Présence locale permanente au Nord et Sud-Kivu.'
            ],
        ]);

        // 3. Featured Services (using Feature model for the top grid)
        $features = [
            ['icon' => 'bi bi-mortarboard', 'title' => 'Éducation de Qualité', 'description' => 'Promouvoir une éducation équitable pour tous les enfants orphelins, formelle et spécialisée.'],
            ['icon' => 'bi bi-heart-pulse', 'title' => 'Bien-être Social', 'description' => 'Soutien aux familles économiquement défavorisées pour les aider à devenir autonomes.'],
            ['icon' => 'bi bi-flower1', 'title' => 'Sécurité Alimentaire', 'description' => 'Projets agricoles respectueux de l\'environnement visant à améliorer la sécurité alimentaire des ménages.'],
            ['icon' => 'bi bi-shield-check', 'title' => 'Environnement', 'description' => 'Initiatives de préservation de l\'environnement et protection des ressources naturelles.'],
            ['icon' => 'bi bi-gear-wide-connected', 'title' => 'Formation Pro', 'description' => 'Renforcement des capacités et développement des compétences professionnelles communautaires.'],
            ['icon' => 'bi bi-cash-coin', 'title' => 'Micro-crédit', 'description' => 'Accompagnement financier pour le développement des activités génératrices de revenus.'],
        ];

        foreach ($features as $index => $f) {
            Feature::create([
                'icon' => $f['icon'],
                'title' => $f['title'],
                'description' => $f['description'],
                'order' => $index,
            ]);
        }

        // 4. Main Services (from the services section)
        $services = [
            [
                'title' => 'Scolarisation des Orphelins',
                'description' => 'Prise en charge scolaire complète dès le primaire jusqu\'au diplôme d\'État.',
                'icon' => 'bi bi-book',
                'features' => ['Frais scolaires', 'Fournitures scolaires', 'Suivi pédagogique', 'Orientation pro'],
                'price' => 'Donation'
            ],
            [
                'title' => 'Sécurité Alimentaire',
                'description' => 'Accompagnement des petits producteurs et familles agricoles.',
                'icon' => 'bi bi-tree',
                'features' => ['Bonnes pratiques agricoles', 'Semences améliorées', 'Équipements agricoles', 'Protection environnementale'],
                'price' => 'Projet'
            ],
            [
                'title' => 'Autonomisation Financière',
                'description' => 'Micro-crédits pour booster les activités génératrices de revenus.',
                'icon' => 'bi bi-bank',
                'features' => ['Micro-crédit solidaire', 'Gestion financière', 'Accompagnement AGR', 'Épargne communautaire'],
                'price' => 'Support'
            ],
        ];

        foreach ($services as $s) {
            Service::create([
                'title' => $s['title'],
                'slug' => Str::slug($s['title']),
                'description' => $s['description'],
                'icon' => $s['icon'],
                'features' => $s['features'],
                'price' => $s['price'],
                'order' => 0
            ]);
        }

        // 5. Projects (Secteurs d'Intervention)
        $projects = [
            ['title' => 'Éducation & Parrainage', 'category' => 'education', 'image' => 'flexbiz/assets/img/portfolio/portfolio-portrait-1.webp'],
            ['title' => 'Sécurité Alimentaire', 'category' => 'agriculture', 'image' => 'flexbiz/assets/img/portfolio/portfolio-2.webp'],
            ['title' => 'Micro-crédit Solidaire', 'category' => 'finance', 'image' => 'flexbiz/assets/img/portfolio/portfolio-portrait-2.webp'],
            ['title' => 'Santé Communautaire', 'category' => 'health', 'image' => 'flexbiz/assets/img/portfolio/portfolio-portrait-4.webp'],
        ];

        foreach ($projects as $p) {
            Project::updateOrCreate(
                ['slug' => Str::slug($p['title'])],
                [
                    'title' => $p['title'],
                    'category' => $p['category'],
                    'image' => $p['image'],
                ]
            );
        }

        // 6. Team Members
        $team = [
            ['name' => 'Coordonnateur Général', 'role' => 'Visionnaire & Fondateur', 'bio' => 'Dédié à la transformation sociale et à l\'éducation des enfants vulnérables en RDC.', 'image' => 'flexbiz/assets/img/person/person-m-7.webp'],
            ['name' => 'Responsable des Programmes', 'role' => 'Coordination Éducation', 'bio' => 'Experte en suivi pédagogique et accompagnement des enfants orphelins.', 'image' => 'flexbiz/assets/img/person/person-f-9.webp'],
            ['name' => 'Expert Agronome', 'role' => 'Sécurité Alimentaire', 'bio' => 'Spécialiste des pratiques agricoles durables et de l\'encadrement des petits producteurs.', 'image' => 'flexbiz/assets/img/person/person-m-11.webp'],
            ['name' => 'Chargée de Micro-finance', 'role' => 'Autonomisation des Femmes', 'bio' => 'Accompagne les ménages dans la gestion des micro-crédits et des AGR.', 'image' => 'flexbiz/assets/img/person/person-f-12.webp'],
        ];

        foreach ($team as $m) {
            TeamMember::create([
                'name'        => $m['name'],
                'position'    => $m['role'],
                'photo'       => $m['image'],
                'description' => $m['bio'],
            ]);
        }

        // 7. Testimonials
        $testimonials = [
            ['name' => 'Bénéficiaire Éducation', 'role' => 'Étudiant diplômé', 'content' => 'Grâce à la Fondation Wezesha, j\'ai pu terminer mes études secondaires malgré les difficultés liées à la guerre.', 'image' => 'flexbiz/assets/img/person/person-f-1.webp'],
            ['name' => 'Producteur Local', 'role' => 'Agriculteur au Sud-Kivu', 'content' => 'L\'accompagnement en semences et en matériel a doublé ma production cette année. Ma famille mange désormais à sa faim.', 'image' => 'flexbiz/assets/img/person/person-m-4.webp'],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create([
                'author_name' => $t['name'],
                'author_position' => $t['role'],
                'content' => $t['content'],
                'author_photo' => $t['image'],
            ]);
        }

        // 8. Partners
        foreach (range(1, 6) as $i) {
            Partner::create([
                'name' => "Client $i",
                'logo' => "flexbiz/assets/img/clients/clients-$i.webp",
            ]);
        }

        // 9. Stats (Counters)
        $stats = [
            ['title' => 'Orphelins Scolarisés', 'value' => '120+', 'icon' => 'bi bi-mortarboard'],
            ['title' => 'Ménages Accompagnés', 'value' => '10000+', 'icon' => 'bi bi-house-heart'],
            ['title' => 'Petits Producteurs', 'value' => '900+', 'icon' => 'bi bi-patch-check'],
            ['title' => 'Zones d\'Impact', 'value' => '2', 'icon' => 'bi bi-geo-alt'],
        ];

        foreach ($stats as $a) {
            \App\Models\Stat::create([
                'title'       => $a['title'],
                'value'       => $a['value'],
                'icon'        => $a['icon'],
                'description' => $a['description'] ?? null,
            ]);
        }

        // 10. FAQs
        $faqs = [
            ['question' => 'Comment la fondation sélectionne-t-elle les bénéficiaires ?', 'answer' => 'Nous travaillons en étroite collaboration avec les leaders communautaires et les services sociaux pour identifier les enfants orphelins et les familles les plus vulnérables victimes des conflits.'],
            ['question' => 'Comment puis-je soutenir vos actions ?', 'answer' => 'Vous pouvez nous soutenir par des dons financiers, matériels ou en devenant partenaire de nos projets agricoles et éducatifs.'],
            ['question' => 'Où intervenez-vous principalement ?', 'answer' => 'Nos activités se concentrent actuellement dans les provinces du Nord-Kivu et du Sud-Kivu, en République Démocratique du Congo.'],
        ];

        foreach ($faqs as $f) {
            Faq::create([
                'question' => $f['question'],
                'answer' => $f['answer'],
            ]);
        }

        // 11. Why Us
        WhyUs::create([
            'title' => 'Pourquoi nous soutenir ?',
            'subtitle' => 'Notre Engagement',
            'intro_title' => 'Un impact réel et mesurable',
            'intro_description' => 'Nous nous concentrons sur des solutions durables pour briser le cycle de la pauvreté et de l\'analphabétisme chez les orphelins de guerre en RDC.',
            'intro_image' => 'flexbiz/assets/img/features-3.jpg',
            'intro_highlights' => [
                'Transparence totale des fonds',
                'Approche centrée sur l\'enfant',
                'Enracinement communautaire'
            ],
            'assurance_title' => 'Votre don change des vies',
            'assurance_description' => '90% de nos ressources sont directement affectées aux programmes sur le terrain.'
        ]);

        // 12. Call To Action (CTA)
        Cta::create([
            'label' => 'Rejoignez le mouvement',
            'title_main' => 'Prêt à faire une',
            'title_accent' => 'différence ?',
            'description' => 'Votre soutien, qu\'il soit financier ou bénévole, est le moteur de notre changement. Ensemble, construisons un avenir meilleur pour les enfants de la RDC.',
            'benefits' => [
                'Éducation garantie pour un orphelin',
                'Autonomie d\'une famille agricole',
                'Impact direct dans les communautés'
            ],
            'button_text' => 'Faire un don maintenant',
            'button_url' => '#',
            'phone' => '+243 978 654 321',
            'image' => 'flexbiz/assets/img/cta-bg.jpg'
        ]);

        // 13. Headers
        SkillHeader::create([
            'title' => 'Notre Expertise Métier',
            'description' => 'Un savoir-faire multidisciplinaire au service du développement communautaire et de l\'autonomisation des populations vulnérables en RDC.',
            'certifications' => ['Agrément National', 'Partenaire PAM', 'Impact Social Certifié']
        ]);

        ServiceHeader::create([
            'title' => 'Secteurs d\'Intervention',
            'subtitle' => 'Des solutions concrètes pour l\'autonomisation des populations vulnérables.'
        ]);

        $sectionHeaders = [
            ['section_key' => 'stats', 'title' => 'Notre Impact en Chiffres', 'subtitle' => 'Résultats'],
            ['section_key' => 'projects', 'title' => 'Secteurs d\'Intervention', 'subtitle' => 'Nos Projets'],
            ['section_key' => 'achievements', 'title' => 'Nos Réalisations', 'subtitle' => 'Impact'],
            ['section_key' => 'team', 'title' => 'Les Visages de Wezesha', 'subtitle' => 'Notre Équipe'],
            ['section_key' => 'testimonials', 'title' => 'Ils Témoignent de l\'Impact', 'subtitle' => 'Témoignages'],
            ['section_key' => 'faq', 'title' => 'Questions Fréquentes', 'subtitle' => 'FAQ'],
            ['section_key' => 'blog', 'title' => 'Dernières Actualités', 'subtitle' => 'Notre Blog'],
        ];

        foreach ($sectionHeaders as $header) {
            SectionHeader::updateOrCreate(['section_key' => $header['section_key']], $header);
        }

        // 14. Skills
        $skills = [
            ['title' => 'Éducation & Parrainage', 'percentage' => 95, 'description' => 'Taux de réussite scolaire des enfants parrainés.'],
            ['title' => 'Sécurité Alimentaire', 'percentage' => 85, 'description' => 'Augmentation moyenne des récoltes des familles accompagnées.'],
            ['title' => 'Autonomisation', 'percentage' => 90, 'description' => 'Taux de remboursement des micro-crédits solidaires.'],
            ['title' => 'Santé Communautaire', 'percentage' => 80, 'description' => 'Couverture des soins de base dans nos zones d\'intervention.'],
        ];

        foreach ($skills as $index => $s) {
            Skill::updateOrCreate(
                ['title' => $s['title']],
                [
                    'percentage' => $s['percentage'],
                    'description' => $s['description'],
                    'order' => $index
                ]
            );
        }
    }
}
