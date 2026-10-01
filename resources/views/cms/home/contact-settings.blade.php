<section class="card card-body my-4" id="coordonnees">
<h2 class="h5">Coordonnées de contact</h2>
<p>Les adresses sont communes à la page Contact et au pied de page. Saisissez une adresse par ligne ; les retours à la ligne sont conservés.</p>
<form method="post" action="{{ route('admin.cms.home.contact') }}">
@csrf @method('PUT')
@foreach(['email' => 'E-mail', 'phone' => 'Téléphone'] as $field => $label)
<label class="form-label" for="contact-{{ $field }}">{{ $label }}</label>
<input class="form-control mb-3" type="{{ $field === 'email' ? 'email' : 'text' }}" id="contact-{{ $field }}" name="{{ $field }}" value="{{ old($field, $settings['adpdh.contact.'.$field] ?? ($field === 'email' ? 'contact@adpdh.org' : '+243 896 263 558')) }}" required>
@endforeach
<label class="form-label" for="contact-address">Adresses — une par ligne</label>
<textarea class="form-control mb-2" id="contact-address" name="address" rows="6" maxlength="2000" aria-describedby="contact-address-help" required>{{ old('address', \App\Support\ContactAddresses::text($settings['adpdh.contact.address'] ?? null)) }}</textarea>
<p class="form-text" id="contact-address-help">Appuyez sur Entrée entre Kinshasa, Bukavu et Uvira. Les tirets dans « Sud-Kivu » restent inchangés.</p>
<button class="btn btn-primary" type="submit">Enregistrer les coordonnées</button>
</form>
</section>
<section class="card card-body my-4" id="bandeau-contact">
<h2 class="h5">Bandeau de contact au-dessus du menu</h2>
<p>Ce texte s’affiche en une ligne de texte continue, sans les retours à la ligne des adresses du pied de page.</p>
<form method="post" action="{{ route('admin.cms.home.topbar') }}">
@csrf @method('PUT')
<label class="form-label" for="contact-topbar">Adresses du bandeau</label>
<input class="form-control mb-2" type="text" id="contact-topbar" name="topbar" maxlength="500" value="{{ old('topbar', $settings['adpdh.contact.topbar'] ?? \App\Support\ContactAddresses::TOPBAR) }}" aria-describedby="contact-topbar-help" required>
<p class="form-text" id="contact-topbar-help">Séparateur : •. Les deux-points sont automatiquement remplacés par ce point. Le téléphone et l’e-mail se modifient dans le bloc ci-dessus.</p>
<button class="btn btn-primary" type="submit">Enregistrer le bandeau</button>
</form>
</section>
