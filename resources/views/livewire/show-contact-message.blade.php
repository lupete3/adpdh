<div>
    <div class="py-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Message de {{ $message->name }}</h3>
                <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i>
                    Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Nom:</strong> {{ $message->name }}
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong> <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <strong>Sujet:</strong> {{ $message->subject }}
                    </li>
                    <li class="list-group-item">
                        <strong>Reçu le:</strong> {{ $message->created_at->format('d/m/Y à H:i') }}
                    </li>
                </ul>
                <div class="mt-4 p-3 border rounded">
                    <p class="mb-0">{!! nl2br(e($message->message)) !!}</p>
                </div>
            </div>
            <div class="card-footer text-end">
                <button wire:click="$dispatch('delete-message', { messageId: {{ $message->id }} })" class="btn btn-danger">
                    <i class="fa fa-trash me-1"></i>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('delete-message', ({ messageId }) => {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
                @this.call('delete', messageId).then(() => {
                    window.location.href = '{{ route('admin.messages.index') }}';
                });
            }
        });
    });
</script>
@endscript
