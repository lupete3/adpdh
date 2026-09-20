<?php

namespace App\Http\Controllers;

use App\Models\MailSetting;
use App\Services\ContactSmtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class MailSettingsController extends Controller
{
    public function edit()
    {
        $settings = MailSetting::find(1);

        return view('cms.mail-settings', [
            'mailSettings' => $settings,
            'values' => $settings?->toArray() ?? [
                'host' => 'smtp.hostinger.com', 'port' => 465, 'encryption' => 'ssl',
                'username' => 'contact@adpdh.org', 'from_address' => 'contact@adpdh.org', 'from_name' => 'ADPDH',
            ],
        ]);
    }

    public function update(Request $request, ContactSmtp $smtp)
    {
        $settings = MailSetting::find(1) ?? new MailSetting;
        $validator = Validator::make($request->all(), [
            'host' => ['required', 'string', 'max:253', 'regex:/^(?=.{1,253}$)(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/D'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'encryption' => ['required', Rule::in(['ssl', 'tls'])],
            'username' => ['required', 'string', 'max:255', 'not_regex:/[\r\n]/'],
            'password' => [$settings->exists ? 'nullable' : 'required', 'string', 'max:2048'],
            'from_address' => ['required', 'email', 'max:254'],
            'from_name' => ['required', 'string', 'max:120', 'not_regex:/[\r\n]/'],
            'action' => ['required', Rule::in(['save', 'test'])],
        ], [
            'required' => 'Le champ :attribute est obligatoire.',
            'email' => 'Saisissez une adresse e-mail valide.',
            'regex' => 'Saisissez un nom de serveur valide, sans https:// ni chemin.',
            'between' => 'Le port doit être compris entre 1 et 65535.',
            'in' => 'La valeur choisie est invalide.',
            'max' => 'Le champ :attribute est trop long.',
            'not_regex' => 'Le champ :attribute ne doit pas contenir de saut de ligne.',
        ], ['host' => 'serveur', 'username' => 'identifiant', 'password' => 'mot de passe', 'from_address' => 'adresse d’expédition', 'from_name' => 'nom d’expéditeur']);

        $safeInput = $request->only('host', 'port', 'encryption', 'username', 'from_address', 'from_name');
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($safeInput);
        }
        $data = $validator->validated();
        $action = $data['action'];
        unset($data['action']);
        if (! $request->filled('password')) unset($data['password']);
        $settings->fill($data);
        $settings->id = 1;

        if ($action === 'test') {
            try {
                $smtp->test($settings);
            } catch (Throwable $exception) {
                // SMTP errors may contain credentials; never expose or log their text.
                return back()->withInput($safeInput)->withErrors(['connection' => 'Connexion impossible. Vérifiez le serveur, le port, le chiffrement et les identifiants. Vérifiez aussi que votre hébergeur autorise les connexions SMTP sortantes.']);
            }

            return back()->withInput($safeInput)->with('status', 'Connexion SMTP réussie. Aucun e-mail n’a été envoyé et aucun réglage n’a été enregistré. Si vous avez saisi un nouveau mot de passe, ressaisissez-le avant d’enregistrer.');
        }

        $settings->save();

        return redirect()->route('admin.cms.mail')->with('status', 'Paramètres enregistrés. Ils seront utilisés dès le prochain message du formulaire de contact.');
    }
}
