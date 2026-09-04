<section id="contact" class="contact section dark-background">
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ $header?->title ?? 'Contact' }}</h2>
        <p>{{ $header?->subtitle ?? 'Besoin d\'aide ? Contactez-nous' }}</p>
    </div>

    <div class="container position-relative" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">

            <div class="col-lg-5">
                <div class="info-item d-flex">
                    <i class="bi bi-geo-alt flex-shrink-0"></i>
                    <div>
                        <h3>Adresse</h3>
                        <p>{{ $settings['address'] ?? 'Bukavu, RD Congo' }}</p>
                    </div>
                </div>
                <div class="info-item d-flex">
                    <i class="bi bi-telephone flex-shrink-0"></i>
                    <div>
                        <h3>Appelez-nous</h3>
                        <p>{{ $settings['phone'] ?? '+243 978 654 321' }}</p>
                    </div>
                </div>
                <div class="info-item d-flex">
                    <i class="bi bi-envelope flex-shrink-0"></i>
                    <div>
                        <h3>Email</h3>
                        <p>{{ $settings['email'] ?? 'contact@wezesha-foundation.org' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <form wire:submit.prevent="save" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <input type="text" name="name" class="form-control" placeholder="Votre Nom" wire:model="name" required>
                            @error('name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control" name="email" placeholder="Votre Email" wire:model="email" required>
                            @error('email') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="subject" placeholder="Sujet" wire:model="subject" required>
                            @error('subject') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-12">
                            <textarea class="form-control" name="message" rows="6" placeholder="Message" wire:model="message" required></textarea>
                            @error('message') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-12 text-center">
                            <div wire:loading class="loading">Chargement</div>
                            
                            @if (session()->has('success'))
                                <div class="sent-message d-block">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="error-message d-block">Veuillez corriger les erreurs dans le formulaire.</div>
                            @endif

                            <button type="submit" wire:loading.attr="disabled">Envoyer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>