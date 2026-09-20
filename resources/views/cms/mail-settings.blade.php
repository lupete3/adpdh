<x-layouts.app>
<div class="mb-4"><h1 class="h3">Messagerie — formulaire de contact</h1><p>Configurez le service qui envoie les messages du formulaire à <strong>contact@adpdh.org</strong>.</p></div>
@include('cms.work.feedback')
<div class="card"><div class="card-body">
    <p>{{ $mailSettings ? 'Une configuration est enregistrée. Les modifications prennent effet après enregistrement.' : 'Aucune configuration enregistrée. Les valeurs proposées correspondent à Hostinger Email.' }}</p>
    <form method="POST" action="{{ route('admin.cms.mail.update') }}">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label" for="smtp-host">Serveur SMTP</label><input class="form-control" id="smtp-host" name="host" value="{{ old('host', $values['host']) }}" required maxlength="253"><small>Exemple : smtp.hostinger.com</small></div>
            <div class="col-md-4"><label class="form-label" for="smtp-port">Port</label><input class="form-control" id="smtp-port" name="port" type="number" min="1" max="65535" value="{{ old('port', $values['port']) }}" required></div>
            <div class="col-md-6"><label class="form-label" for="smtp-encryption">Chiffrement</label><select class="form-select" id="smtp-encryption" name="encryption"><option value="ssl" @selected(old('encryption', $values['encryption']) === 'ssl')>SSL/TLS — généralement port 465</option><option value="tls" @selected(old('encryption', $values['encryption']) === 'tls')>STARTTLS — généralement port 587</option></select></div>
            <div class="col-md-6"><label class="form-label" for="smtp-username">Identifiant de la boîte mail</label><input class="form-control" id="smtp-username" name="username" autocomplete="off" value="{{ old('username', $values['username']) }}" required maxlength="255"></div>
            <div class="col-12"><label class="form-label" for="smtp-password">Mot de passe de la boîte mail</label><input class="form-control" id="smtp-password" name="password" type="password" autocomplete="new-password" maxlength="2048" @required(!$mailSettings) aria-describedby="smtp-password-help"><small id="smtp-password-help">{{ $mailSettings ? 'Mot de passe enregistré et chiffré. Laissez ce champ vide pour le conserver.' : 'Saisissez le mot de passe de la boîte mail, pas celui de votre compte Hostinger.' }} Il ne sera jamais affiché à nouveau.</small></div>
            <div class="col-md-6"><label class="form-label" for="smtp-from-address">Adresse d’expédition</label><input class="form-control" id="smtp-from-address" name="from_address" type="email" value="{{ old('from_address', $values['from_address']) }}" required maxlength="254"><small>Utilisez une adresse autorisée par votre fournisseur de messagerie.</small></div>
            <div class="col-md-6"><label class="form-label" for="smtp-from-name">Nom d’expéditeur</label><input class="form-control" id="smtp-from-name" name="from_name" value="{{ old('from_name', $values['from_name']) }}" required maxlength="120"></div>
        </div>
        <div class="d-flex flex-wrap gap-3 mt-4"><button class="btn btn-primary" type="submit" name="action" value="save">Enregistrer les paramètres</button><button class="btn btn-outline-primary" type="submit" name="action" value="test">Tester la connexion</button></div>
        <p class="mt-3 mb-0">Le test utilise les valeurs du formulaire, sans les enregistrer ni envoyer de mail. Si le mot de passe est laissé vide, il utilise celui déjà enregistré. Une connexion réussie ne garantit pas la réception : après enregistrement, vérifiez aussi l’envoi depuis le formulaire de contact.</p>
    </form>
</div></div>
</x-layouts.app>
