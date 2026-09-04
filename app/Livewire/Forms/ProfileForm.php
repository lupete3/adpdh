<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Livewire\WithFileUploads;

class ProfileForm extends Form
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $photo;

    public function set(User $user): void
    {
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(Auth::id()),
            ],
            'photo' => ['nullable', 'image', 'max:1024'], // 1MB Max
        ];
    }

    public function update(): void
    {
        $this->validate();

        $user = Auth::user();

        $user->fill($this->only(['name', 'email']));

        if ($this->photo) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $this->photo->store('photos', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }
}