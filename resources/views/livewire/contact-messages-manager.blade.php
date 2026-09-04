<div>
    <div class="py-5">
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Messages de Contact Reçus</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Sujet</th>
                                <th>Message</th>
                                <th>Reçu le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($messages as $message)
                                <tr>
                                    <td>{{ $message->name }}</td>
                                    <td>{{ $message->email }}</td>
                                    <td>{{ $message->subject }}</td>
                                    <td>{{ Str::limit($message->message, 50) }}</td>
                                    <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        
                                        <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-info btn-sm">
                                            <i class="bx bx-search"></i>
                                        </a>
                                        <button wire:click="delete({{ $message->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce message ?" class="btn btn-danger btn-sm">
                                            <i class="bx bx-trash me-1"></i>
                                        </button>
                                    
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucun message pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
