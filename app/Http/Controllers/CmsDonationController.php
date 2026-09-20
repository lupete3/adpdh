<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CmsDonationController extends Controller
{
    public const DEFAULTS = [
        'page_title' => 'Faire un don',
        'eyebrow' => 'Faire un don',
        'heading' => 'Soutenir l’autonomie.',
        'heading_accent' => 'Faire grandir la solidarité.',
        'introduction' => 'Votre soutien contribue aux actions auprès des communautés accompagnées par ADPDH.',
        'purpose_title' => 'Accompagner des initiatives concrètes.',
        'purpose_text' => 'Épargne communautaire, activités génératrices de revenus, protection et cohésion sociale : découvrez les domaines dans lesquels ADPDH agit.',
        'activities_label' => 'Découvrir les activités →',
        'bank_eyebrow' => 'DON PAR VIREMENT',
        'bank_title' => 'Faire un don par virement.',
        'bank_intro' => 'Vous pouvez soutenir ADPDH par virement sur le compte ci-dessous.',
        'bank_label' => 'Banque',
        'bank' => 'Equity BCDC',
        'account_name_label' => 'Intitulé du compte',
        'account_name' => 'Action pour Le Developpement Et La Promotions Des Droits Humains',
        'account_number_label' => 'Numéro de compte',
        'account_number' => '400200086445762',
        'contact_text' => 'Pour toute information complémentaire sur votre virement ou pour préciser l’action que vous souhaitez soutenir, contactez notre équipe.',
        'contact_label' => 'Nous contacter pour un don ↗',
        'phone' => '+243 896 263 558',
        'notice' => 'Le virement s’effectue auprès de votre banque. Aucun paiement n’est effectué directement sur ce site.',
        'faq_title' => 'VOS QUESTIONS',
        'question_1' => 'Comment préciser l’action que je souhaite soutenir ?',
        'answer_1' => 'Indiquez votre domaine ou votre projet d’intérêt dans votre message à ADPDH. L’équipe pourra préciser avec vous les modalités d’affectation.',
        'question_2' => 'Puis-je apporter du matériel ou une expertise ?',
        'answer_2' => 'Oui, ces formes de contribution sont présentées dans les types de partenariat recherchés.',
        'partnership_label' => 'Découvrir les partenariats →',
        'question_3' => 'Comment obtenir des informations sur les résultats ?',
        'answer_3' => 'Les indicateurs disponibles et leurs sources sont regroupés sur la page Impact.',
        'impact_label' => 'Consulter notre impact →',
        'email' => 'contact@adpdh.org',
        'email_subject' => 'Soutenir ADPDH par un don',
    ];

    public const LABELS = [
        'page_title' => 'Titre de la page',
        'eyebrow' => 'Surtitre',
        'heading' => 'Titre principal',
        'heading_accent' => 'Titre en couleur',
        'introduction' => 'Introduction',
        'purpose_title' => 'Titre de la présentation',
        'purpose_text' => 'Présentation des actions',
        'activities_label' => 'Lien vers les activités',
        'bank_eyebrow' => 'Surtitre du virement',
        'bank_title' => 'Titre du virement',
        'bank_intro' => 'Instructions de virement',
        'bank_label' => 'Libellé de la banque',
        'bank' => 'Banque',
        'account_name_label' => 'Libellé du titulaire',
        'account_name' => 'Intitulé du compte',
        'account_number_label' => 'Libellé du numéro de compte',
        'account_number' => 'Numéro de compte',
        'contact_text' => 'Texte de contact',
        'contact_label' => 'Bouton de contact',
        'phone' => 'Téléphone',
        'notice' => 'Note sur le paiement',
        'faq_title' => 'Titre des questions fréquentes',
        'question_1' => 'Question 1',
        'answer_1' => 'Réponse 1',
        'question_2' => 'Question 2',
        'answer_2' => 'Réponse 2',
        'partnership_label' => 'Lien vers les partenariats',
        'question_3' => 'Question 3',
        'answer_3' => 'Réponse 3',
        'impact_label' => 'Lien vers l’impact',
        'email' => 'Adresse e-mail pour les dons',
        'email_subject' => 'Objet de l’e-mail',
    ];

    private function data(): array
    {
        $settings = DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key');
        $copy = collect(self::DEFAULTS)->map(fn ($value, $key) => $settings['adpdh.donation.'.$key] ?? $value);

        return compact('settings', 'copy');
    }

    public function show()
    {
        return view('adpdh.donation', $this->data() + [
            'footerSections' => CmsPage::where('key', 'index')->first()?->sections()->with('contents')->get() ?? collect(),
        ]);
    }

    public function edit()
    {
        return view('cms.donation.edit', $this->data() + ['labels' => self::LABELS]);
    }

    public function update(Request $request)
    {
        $rules = array_fill_keys(array_keys(self::DEFAULTS), 'required|string|max:2000');
        $rules['email'] = 'required|email|max:255';
        $rules['phone'] = ['required', 'string', 'max:60', 'regex:/^[+0-9(). \-]+$/'];
        $rules['account_number'] = ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9 .\/-]+$/'];
        $rules['bank'] = $rules['account_name'] = 'required|string|max:255';
        $data = $request->validate($rules);
        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                DB::table('settings')->updateOrInsert(['key' => 'adpdh.donation.'.$key], ['value' => $value, 'updated_at' => now()]);
            }
        });

        return redirect()->route('admin.cms.donation')->with('status', 'Page de don mise à jour.');
    }
}
