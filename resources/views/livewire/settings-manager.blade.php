<div>
    <div class="flex-grow-1">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Pages /</span> Paramètres du site
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Gestion des informations du site</h5>
                        <a href="{{ route('admin.section-headers.edit', 'contact') }}" class="btn btn-outline-primary btn-sm" wire:navigate>Modifier l'en-tête Contact</a>
                    </div>
                    <div class="card-body">
                        @if (session()->has('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="site_name" class="form-label">Nom du site</label>
                                    <input type="text" class="form-control" id="site_name" wire:model="site_name">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" id="phone" wire:model="phone">
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label for="slogan" class="form-label">Slogan</label>
                                    <textarea class="form-control" id="slogan" rows="3" wire:model="slogan"></textarea>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="address" class="form-label">Adresse</label>
                                    <input type="text" class="form-control" id="address" wire:model="address">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" wire:model="email">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="twitter_url" class="form-label">URL Twitter</label>
                                    <input type="text" class="form-control" id="twitter_url" wire:model="twitter_url">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="facebook_url" class="form-label">URL Facebook</label>
                                    <input type="text" class="form-control" id="facebook_url" wire:model="facebook_url">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="linkedin_url" class="form-label">URL LinkedIn</label>
                                    <input type="text" class="form-control" id="linkedin_url" wire:model="linkedin_url">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input type="file" class="form-control" id="logo" wire:model="logo">
                                    @if ($existing_logo)
                                        <img src="{{ asset('storage/' . $existing_logo) }}" alt="Logo" class="img-thumbnail mt-2" width="150">
                                    @endif
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="feature_image" class="form-label">Image "Features"</label>
                                    <input type="file" class="form-control" id="feature_image" wire:model="feature_image">
                                    @if ($existing_feature_image)
                                        <img src="{{ asset('storage/' . $existing_feature_image) }}" alt="Feature Image" class="img-thumbnail mt-2" width="150">
                                    @endif
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Enregistrer</span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Enregistrement...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
