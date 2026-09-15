<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('adpdh:grant-admin {email}', function () {
    $user = \App\Models\User::where('email', $this->argument('email'))->first();
    if (! $user) { $this->error('Compte existant introuvable. Aucun compte créé.'); return 1; }
    $user->is_admin = true;
    $user->save();
    $this->info('Accès administrateur activé ; mot de passe conservé.');
})->purpose('Autoriser un compte existant à accéder au CMS');
