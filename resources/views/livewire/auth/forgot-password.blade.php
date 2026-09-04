<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('Un lien de réinitialisation sera envoyé si le compte existe.'));
    }
}; ?>

@section('title', 'Mot de passe oublié')

@section('page-style')
@vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

<div>
    <h4 class="mb-1">{{ __('Mot de passe oublié ?') }} 🔒</h4>
    <p class="mb-6">{{ __('Saisissez votre e-mail et nous vous enverrons des instructions pour réinitialiser votre mot de passe') }}</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="sendPasswordResetLink" class="mb-6">
        <div class="mb-6">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input
                wire:model="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                required
                autofocus
                autocomplete="email"
                placeholder="{{ __('Saisissez votre e-mail') }}"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary d-grid w-100 mb-6" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Envoyer le lien de réinitialisation') }}</span>
            <span wire:loading>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Envoi...') }}
            </span>
        </button>
    </form>

    <div class="text-center">
        <a href="{{ route('login') }}" class="d-flex justify-content-center" wire:navigate>
            <i class="bx bx-chevron-left scaleX-n1-rtl me-1"></i>
            {{ __('Retour à la connexion') }}
        </a>
    </div>
</div>