<?php

namespace App\Http\Controllers;

use App\Models\MailSetting;
use App\Services\ContactSmtp;
use App\Services\ContactPhpMail;
use Illuminate\Support\Facades\DB;
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
            'contactTransport' => DB::table('settings')->where('key', 'adpdh.contact.mail_transport')->value('value') ?? 'smtp',
            'mailSettings' => $settings,
            'values' => $settings?->toArray() ?? [
                'host' => 'smtp.hostinger.com', 'port' => 465, 'encryption' => 'ssl',
                'username' => 'contact@adpdh.org', 'from_address' => 'contact@adpdh.org', 'from_name' => 'ADPDH',
            ],
        ]);
    }

    public function update(Request $request, ContactSmtp $smtp)
    {
        if (in_array($request->input('action'), ['php_mail_save', 'php_mail_test'], true)) {
            if ($request->input('action') === 'php_mail_test') {
                try {
                    app(ContactPhpMail::class)->test();
                } catch (\App\Exceptions\ContactMailException $exception) {
                    return back()->withErrors(['connection' => '['.$exception->diagnostic.'] '.$exception->explanation().' Aucun réglage n’a été modifié.']);
                } catch (Throwable $exception) {
                    $reference = (string) \Illuminate\Support\Str::uuid();
                    \Illuminate\Support\Facades\Log::error('Contact PHP test failed before acceptance.', ['reference' => $reference, 'exception_type' => get_class($exception), 'file' => basename($exception->getFile()), 'line' => $exception->getLine()]);
                    return back()->withErrors(['connection' => '[PHP_MAIL_APPLICATION_ERROR] Une erreur de l’application a interrompu le test. Ce résultat ne prouve pas un refus de l’hébergeur. Référence du journal : '.$reference.'. Aucun réglage n’a été modifié.']);
                }
                return back()->with('status', 'Le serveur a accepté le message de test pour contact@adpdh.org. Vérifiez la boîte de réception et les indésirables. Aucun réglage n’a été modifié.');
            }
            DB::table('settings')->updateOrInsert(['key' => 'adpdh.contact.mail_transport'], ['value' => 'php_mail', 'updated_at' => now()]);
            return redirect()->route('admin.cms.mail')->with('status', 'Envoi PHP activé. Le formulaire utilisera le service mail de l’hébergement pour écrire à contact@adpdh.org.');
        }

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

        DB::transaction(function () use ($settings) {
            $settings->save();
            DB::table('settings')->where('key', 'adpdh.contact.mail_transport')->delete();
        });

        return redirect()->route('admin.cms.mail')->with('status', 'Paramètres enregistrés. Ils seront utilisés dès le prochain message du formulaire de contact.');
    }
}
