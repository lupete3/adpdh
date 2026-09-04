<?php

use App\\\Livewire\\Actions\\Logout;
use Illuminate\\\\Support\\\\Facades\\\\Auth;
use Illuminate\\\\Support\\\\Facades\\\\Session;
use Livewire\\\\Attributes\\\\Layout;
use Livewire\\\\Volt\\\\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
};
?>

@section('title', 'Vérifier l\'e-mail')

@section('page-style')
@vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

<div>
    <h4 class="mb-1">{{ __('Vérifiez votre e-mail') }} 📧</h4>
    <p class="mb-6">{{ __('Veuillez vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer.') }}</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4">
            {{ __('Un nouveau lien de vérification a été envoyé à l\'adresse e-mail que vous avez fournie lors de l\'inscription.') }}
        </div>
    @endif

    <div class="text-center mb-6">
        <button wire:click="sendVerification" class="btn btn-primary d-grid w-100 mb-3" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Renvoyer l\'e-mail de vérification') }}</span>
            <span wire:loading>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Envoi...') }}
            </span>
        </button>

        <button wire:click="logout" class="btn btn-link" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Déconnexion') }}</span>
            <span wire:loading>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Déconnexion...') }}
            </span>
        </button>
    </div>
</div>