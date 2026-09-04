<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

// Section Pages
Route::get('/about', \App\Livewire\AboutPage::class)->name('about');
Route::get('/features', \App\Livewire\FeaturesPage::class)->name('features');
Route::get('/achievements', \App\Livewire\AchievementsPage::class)->name('achievements');
Route::get('/team', \App\Livewire\TeamPage::class)->name('team');
Route::get('/blog', \App\Livewire\PostsPage::class)->name('blog');
Route::get('/contact', \App\Livewire\ContactPage::class)->name('contact');
Route::get('/publications/{category?}', \App\Livewire\PublicationsPage::class)->name('publications');
Route::get('/careers', \App\Livewire\CareersPage::class)->name('careers');
Volt::route('/galerie', 'gallery-page')->name('gallery');


// Detail Pages
Route::get('/blog/{id}', \App\Livewire\BlogDetail::class)->name('blog.detail');
Route::get('/achievements/{id}', \App\Livewire\AchievementDetail::class)->name('achievement.detail');

Volt::route('dashboard', 'admin.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Posts
    Volt::route('posts', 'admin.posts.index')->name('posts.index');
    Volt::route('posts/create', 'admin.posts.create')->name('posts.create');
    Volt::route('posts/{post}/edit', 'admin.posts.edit')->name('posts.edit');

    // Team Members
    Volt::route('team-members', 'admin.team-members.index')->name('team-members.index');
    Volt::route('team-members/create', 'admin.team-members.create')->name('team-members.create');
    Volt::route('team-members/{teamMember}/edit', 'admin.team-members.edit')->name('team-members.edit');

    // Achievements
    Volt::route('achievements', 'admin.achievements.index')->name('achievements.index');
    Volt::route('achievements/create', 'admin.achievements.create')->name('achievements.create');
    Volt::route('achievements/{achievement}/edit', 'admin.achievements.edit')->name('achievements.edit');

    // Sliders
    Volt::route('sliders', 'admin.sliders.index')->name('sliders.index');
    Volt::route('sliders/create', 'admin.sliders.create')->name('sliders.create');
    Volt::route('sliders/{slider}/edit', 'admin.sliders.edit')->name('sliders.edit');

    // Partners
    Volt::route('partners', 'admin.partners.index')->name('partners.index');
    Volt::route('partners/create', 'admin.partners.create')->name('partners.create');
    Volt::route('partners/{partner}/edit', 'admin.partners.edit')->name('partners.edit');

    // Features
    Volt::route('features', 'admin.features.index')->name('features.index');
    Volt::route('features/create', 'admin.features.create')->name('features.create');
    Volt::route('features/{feature}/edit', 'admin.features.edit')->name('features.edit');

    // Testimonials
    Volt::route('testimonials', 'admin.testimonials.index')->name('testimonials.index');
    Volt::route('testimonials/create', 'admin.testimonials.create')->name('testimonials.create');
    Volt::route('testimonials/{testimonial}/edit', 'admin.testimonials.edit')->name('testimonials.edit');

    // Services
    Volt::route('services', 'admin.services.index')->name('services.index');
    Volt::route('services/create', 'admin.services.create')->name('services.create');
    Volt::route('services/header/edit', 'admin.services.header_edit')->name('services.header.edit');
    Volt::route('services/{service}/edit', 'admin.services.edit')->name('services.edit');

    // Projects
    Volt::route('projects', 'admin.projects.index')->name('projects.index');
    Volt::route('projects/create', 'admin.projects.create')->name('projects.create');
    Volt::route('projects/{project}/edit', 'admin.projects.edit')->name('projects.edit');

    // FAQs
    Volt::route('faqs', 'admin.faqs.index')->name('faqs.index');
    Volt::route('faqs/create', 'admin.faqs.create')->name('faqs.create');
    Volt::route('faqs/{faq}/edit', 'admin.faqs.edit')->name('faqs.edit');

    // Stats
    Volt::route('stats', 'admin.stats.index')->name('stats.index');
    Volt::route('stats/create', 'admin.stats.create')->name('stats.create');
    Volt::route('stats/{stat}/edit', 'admin.stats.edit')->name('stats.edit');

    // About Page
    Volt::route('about/edit', 'admin.about.edit')->name('about.edit');

    // Call to Action
    Volt::route('cta/edit', 'admin.cta.edit')->name('cta.edit');

    // Why Us
    Volt::route('why-us/edit', 'admin.why-us.edit')->name('why-us.edit');

    // Skills
    Volt::route('skills', 'admin.skills.index')->name('skills.index');
    Volt::route('skills/create', 'admin.skills.create')->name('skills.create');
    Volt::route('skills/header/edit', 'admin.skills.header_edit')->name('skills.header.edit');
    Volt::route('skills/{skill}/edit', 'admin.skills.edit')->name('skills.edit');

    // Publications
    Volt::route('publications', 'admin.publications.index')->name('publications.index');
    Volt::route('publications/create', 'admin.publications.create')->name('publications.create');
    Volt::route('publications/{publication}/edit', 'admin.publications.edit')->name('publications.edit');

    // Job Openings
    Volt::route('job-openings', 'admin.job-openings.index')->name('job-openings.index');
    Volt::route('job-openings/create', 'admin.job-openings.create')->name('job-openings.create');
    Volt::route('job-openings/{jobOpening}/edit', 'admin.job-openings.edit')->name('job-openings.edit');

    // Gallery Photos
    Volt::route('gallery-photos', 'admin.gallery-photos.index')->name('gallery-photos.index');
    Volt::route('gallery-photos/create', 'admin.gallery-photos.create')->name('gallery-photos.create');
    Volt::route('gallery-photos/{galleryPhoto}/edit', 'admin.gallery-photos.edit')->name('gallery-photos.edit');

    // Settings
    Route::get('settings', \App\Livewire\SettingsManager::class)->name('settings');

    // Section Headers
    Volt::route('section-headers/{section}/edit', 'admin.section-headers.edit')->name('section-headers.edit');

    // Contact Messages
    Route::get('messages', \App\Livewire\ContactMessagesManager::class)->name('messages.index');
    Route::get('messages/{message}', \App\Livewire\ShowContactMessage::class)->name('messages.show');
});

