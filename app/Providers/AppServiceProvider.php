<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            $settings = Setting::all()->keyBy('key');
            view()->share('settings', $settings);
        }

        // Global helper: resolve media paths from either public/ or storage/
        Blade::directive('mediaUrl', function ($expression) {
            return "<?php echo media_url($expression); ?>";
        });
    }
}
