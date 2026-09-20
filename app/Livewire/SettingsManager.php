<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettingsManager extends Component
{
    use WithFileUploads;
    use \App\Livewire\Concerns\UsesMediaLibrary;

    public $site_name;
    public $slogan;
    public $address;
    public $email;
    public $phone;
    public $twitter_url;
    public $facebook_url;
    public $linkedin_url;
    public $logo;
    public $feature_image;
    public $existing_logo;
    public $existing_feature_image;

    public function mount()
    {
        $settings = Setting::all()->keyBy('key');
        $this->site_name = $settings['site_name']->value ?? '';
        $this->slogan = $settings['slogan']->value ?? '';
        $this->address = $settings['address']->value ?? '';
        $this->email = $settings['email']->value ?? '';
        $this->phone = $settings['phone']->value ?? '';
        $this->twitter_url = $settings['twitter_url']->value ?? '';
        $this->facebook_url = $settings['facebook_url']->value ?? '';
        $this->linkedin_url = $settings['linkedin_url']->value ?? '';
        $this->existing_logo = $settings['logo']->value ?? '';
        $this->existing_feature_image = $settings['feature_image']->value ?? '';
    }

    public function save()
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $this->validate(['logo' => $this->mediaRule('logo'), 'feature_image' => $this->mediaRule('feature_image')]);
        $settings = [
            'site_name' => $this->site_name,
            'slogan' => $this->slogan,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'twitter_url' => $this->twitter_url,
            'facebook_url' => $this->facebook_url,
            'linkedin_url' => $this->linkedin_url,
        ];

        foreach ($settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        if ($this->mediaChanged('logo')) {
            $logoPath = $this->mediaPath('logo');
            Setting::where('key', 'logo')->update(['value' => $logoPath]);
            $this->existing_logo = $logoPath;
        }

        if ($this->mediaChanged('feature_image')) {
            $featureImagePath = $this->mediaPath('feature_image');
            Setting::where('key', 'feature_image')->update(['value' => $featureImagePath]);
            $this->existing_feature_image = $featureImagePath;
        }

        session()->flash('message', 'Paramètres enregistrés avec succès.');
    }

    public function render()
    {
        return view('livewire.settings-manager');
    }
}
