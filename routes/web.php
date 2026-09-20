<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/ressources', [\App\Http\Controllers\CmsResourceController::class, 'index'])->name('resources');
Route::get('/ressources/{slug}/lire', [\App\Http\Controllers\CmsResourceController::class, 'read'])->name('resources.read');
Route::get('/ressources/{slug}/telecharger', [\App\Http\Controllers\CmsResourceController::class, 'download'])->name('resources.download');
Route::get('/ressources/{slug}', [\App\Http\Controllers\CmsResourceController::class, 'show'])->name('resources.show');
Route::redirect('/ressources.html', '/ressources', 301);
Route::redirect('/adpdh/ressources.html', '/ressources', 301);
Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/resources')->name('admin.cms.resources')->group(function () {
    Route::get('/', [\App\Http\Controllers\CmsResourceController::class, 'manage']);
    Route::get('/create', [\App\Http\Controllers\CmsResourceController::class, 'edit'])->name('.create');
    Route::post('/', [\App\Http\Controllers\CmsResourceController::class, 'save'])->name('.store');
    Route::get('/{resource}/edit', [\App\Http\Controllers\CmsResourceController::class, 'edit'])->name('.edit');
    Route::put('/{resource}', [\App\Http\Controllers\CmsResourceController::class, 'save'])->name('.update');
    Route::delete('/{resource}', [\App\Http\Controllers\CmsResourceController::class, 'destroy'])->name('.destroy');
});


Route::get('/actualites', [\App\Http\Controllers\CmsNewsController::class, 'index'])->name('news');
Route::get('/actualites/{slug}', [\App\Http\Controllers\CmsNewsController::class, 'show'])->name('news.show');
Route::redirect('/actualites.html', '/actualites', 301);
Route::redirect('/adpdh/actualites.html', '/actualites', 301);
Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/news')->name('admin.cms.news')->group(function () {
    Route::get('/', [\App\Http\Controllers\CmsNewsController::class, 'manage']);
    Route::get('/create', [\App\Http\Controllers\CmsNewsController::class, 'edit'])->name('.create');
    Route::post('/', [\App\Http\Controllers\CmsNewsController::class, 'save'])->name('.store');
    Route::get('/{post}/edit', [\App\Http\Controllers\CmsNewsController::class, 'edit'])->name('.edit');
    Route::put('/{post}', [\App\Http\Controllers\CmsNewsController::class, 'save'])->name('.update');
});

Route::get('/devenir-partenaire', [\App\Http\Controllers\CmsPartnershipController::class, 'show'])->name('partnership');
Route::get('/devenir-partenaire/presentation.pdf', [\App\Http\Controllers\CmsPartnershipController::class, 'download'])->name('partnership.download');
Route::redirect('/devenir-partenaire.html', '/devenir-partenaire', 301);
Route::redirect('/adpdh/devenir-partenaire.html', '/devenir-partenaire', 301);
Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/partnership')->name('admin.cms.partnership')->group(function () {
    Route::get('/', [\App\Http\Controllers\CmsPartnershipController::class, 'index']);
    Route::get('/create', [\App\Http\Controllers\CmsPartnershipController::class, 'edit'])->name('.create');
    Route::post('/', [\App\Http\Controllers\CmsPartnershipController::class, 'save'])->name('.store');
    Route::put('/presentation', [\App\Http\Controllers\CmsPartnershipController::class, 'presentation'])->name('.presentation');
    Route::post('/document', [\App\Http\Controllers\CmsPartnershipController::class, 'document'])->name('.document');
    Route::delete('/document', [\App\Http\Controllers\CmsPartnershipController::class, 'removeDocument'])->name('.document.remove');
    Route::get('/{reason}/edit', [\App\Http\Controllers\CmsPartnershipController::class, 'edit'])->name('.edit');
    Route::put('/{reason}', [\App\Http\Controllers\CmsPartnershipController::class, 'save'])->name('.update');
});

Route::get('/devenir-partenaire', [\App\Http\Controllers\CmsPartnershipController::class, 'show'])->name('partnership');
Route::get('/devenir-partenaire/presentation.pdf', [\App\Http\Controllers\CmsPartnershipController::class, 'download'])->name('partnership.download');
Route::redirect('/devenir-partenaire.html', '/devenir-partenaire', 301);
Route::redirect('/adpdh/devenir-partenaire.html', '/devenir-partenaire', 301);
Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/partnership')->name('admin.cms.partnership')->group(function () {
    Route::get('/', [\App\Http\Controllers\CmsPartnershipController::class, 'index']);
    Route::get('/create', [\App\Http\Controllers\CmsPartnershipController::class, 'edit'])->name('.create');
    Route::post('/', [\App\Http\Controllers\CmsPartnershipController::class, 'save'])->name('.store');
    Route::put('/presentation', [\App\Http\Controllers\CmsPartnershipController::class, 'presentation'])->name('.presentation');
    Route::post('/document', [\App\Http\Controllers\CmsPartnershipController::class, 'document'])->name('.document');
    Route::delete('/document', [\App\Http\Controllers\CmsPartnershipController::class, 'removeDocument'])->name('.document.remove');
    Route::get('/{reason}/edit', [\App\Http\Controllers\CmsPartnershipController::class, 'edit'])->name('.edit');
    Route::put('/{reason}', [\App\Http\Controllers\CmsPartnershipController::class, 'save'])->name('.update');
});

Route::get('/notre-impact', [\App\Http\Controllers\CmsImpactController::class, 'show'])->name('impact');
Route::redirect('/impact.html', '/notre-impact', 301);
Route::redirect('/adpdh/impact.html', '/notre-impact', 301);
Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/impact')->name('admin.cms.impact')->group(function () {
    Route::get('/', [\App\Http\Controllers\CmsImpactController::class, 'index']);
    Route::put('/presentation', [\App\Http\Controllers\CmsImpactController::class, 'presentation'])->name('.presentation');
    Route::get('/create', [\App\Http\Controllers\CmsImpactController::class, 'edit'])->name('.create');
    Route::post('/', [\App\Http\Controllers\CmsImpactController::class, 'save'])->name('.store');
    Route::get('/{indicator}/edit', [\App\Http\Controllers\CmsImpactController::class, 'edit'])->name('.edit');
    Route::put('/{indicator}', [\App\Http\Controllers\CmsImpactController::class, 'save'])->name('.update');
});

Route::get('/nos-succes', [\App\Http\Controllers\CmsSuccessController::class, 'show'])->name('success');
Route::redirect('/temoignages.html', '/nos-succes', 301);
Route::redirect('/adpdh/temoignages.html', '/nos-succes', 301);
Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/success')->name('admin.cms.success')->group(function () {
    Route::get('/', [\App\Http\Controllers\CmsSuccessController::class, 'edit']);
    Route::put('/', [\App\Http\Controllers\CmsSuccessController::class, 'save'])->name('.save');
});

Route::get('/activites', [\App\Http\Controllers\CmsActivityController::class, 'index'])->name('activities');
Route::get('/activites/{slug}', [\App\Http\Controllers\CmsActivityController::class, 'show'])->name('activities.show');
Route::get('/activite-{key}.html', [\App\Http\Controllers\CmsActivityController::class, 'legacy']);
Route::get('/adpdh/activite-{key}.html', [\App\Http\Controllers\CmsActivityController::class, 'legacy']);
Route::redirect('/activites.html', '/activites', 301);
Route::redirect('/adpdh/activites.html', '/activites', 301);
Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/activities')->name('admin.cms.activities')->group(function () {
    Route::get('/', [\App\Http\Controllers\CmsActivityController::class, 'manage']);
    Route::get('/create', [\App\Http\Controllers\CmsActivityController::class, 'edit'])->name('.create');
    Route::post('/', [\App\Http\Controllers\CmsActivityController::class, 'save'])->name('.store');
    Route::get('/{activity}/edit', [\App\Http\Controllers\CmsActivityController::class, 'edit'])->name('.edit');
    Route::put('/{activity}', [\App\Http\Controllers\CmsActivityController::class, 'save'])->name('.update');
});

Route::get('/', [\App\Http\Controllers\CmsHomeController::class, 'show'])->name('home');
Route::get('/que-faisons-nous', [\App\Http\Controllers\CmsWorkController::class, 'show'])->name('work');
Route::redirect('/que-faisons-nous.html', '/que-faisons-nous', 301);
Route::redirect('/adpdh/que-faisons-nous.html', '/que-faisons-nous', 301);
Route::get('/qui-sommes-nous', [\App\Http\Controllers\CmsAboutController::class, 'show'])->name('organization');
Route::redirect('/qui-sommes-nous.html', '/qui-sommes-nous', 301);
Route::redirect('/adpdh/qui-sommes-nous.html', '/qui-sommes-nous', 301);

// Section Pages
Route::get('/about', \App\Livewire\AboutPage::class)->name('about');
Route::get('/features', \App\Livewire\FeaturesPage::class)->name('features');
Route::get('/achievements', \App\Livewire\AchievementsPage::class)->name('achievements');
Route::get('/team', \App\Livewire\TeamPage::class)->name('team');
Route::get('/blog', \App\Livewire\PostsPage::class)->name('blog');
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.send');
Route::redirect('/contact.html', '/contact', 301);
Route::redirect('/adpdh/contact.html', '/contact', 301);
Route::get('/publications/{category?}', fn (?string $category = null) => redirect()->route('resources', $category ? ['category' => $category] : [], 301))->name('publications');
Route::get('/careers', \App\Livewire\CareersPage::class)->name('careers');
Volt::route('/galerie', 'gallery-page')->name('gallery');


// Detail Pages
Route::get('/blog/{id}', \App\Livewire\BlogDetail::class)->name('blog.detail');
Route::get('/achievements/{id}', \App\Livewire\AchievementDetail::class)->name('achievement.detail');

Volt::route('dashboard', 'admin.dashboard')
    ->middleware(['auth', 'verified', \App\Http\Middleware\EnsureAdministrator::class])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin')->name('admin.')->group(function () {
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


Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms')->name('admin.cms.')->group(function () {
 Route::get('media', [\App\Http\Controllers\MediaLibraryController::class, 'index'])->name('media.index');
 Route::post('media', [\App\Http\Controllers\MediaLibraryController::class, 'store'])->middleware('throttle:60,1')->name('media.store');
 Route::get('media/{asset}', [\App\Http\Controllers\MediaLibraryController::class, 'show'])->name('media.show');
 Route::put('media/{asset}', [\App\Http\Controllers\MediaLibraryController::class, 'update'])->name('media.update');
 Route::delete('media/{asset}', [\App\Http\Controllers\MediaLibraryController::class, 'destroy'])->name('media.destroy');
 Route::get('mail', [\App\Http\Controllers\MailSettingsController::class, 'edit'])->name('mail');
 Route::put('mail', [\App\Http\Controllers\MailSettingsController::class, 'update'])->middleware('throttle:10,1')->name('mail.update');
 Route::get('work', [\App\Http\Controllers\CmsWorkController::class, 'index'])->name('work');
 Route::put('work/seo', [\App\Http\Controllers\CmsWorkController::class, 'seo'])->name('work.seo');
 Route::get('work/sections/{section}', [\App\Http\Controllers\CmsWorkController::class, 'section'])->name('work.section');
 Route::put('work/sections/{section}', [\App\Http\Controllers\CmsHomeController::class, 'updateSection'])->name('work.section.update');
 Route::get('work/pillars/create', [\App\Http\Controllers\CmsWorkController::class, 'editPillar'])->name('work.pillars.create');
 Route::post('work/pillars', [\App\Http\Controllers\CmsWorkController::class, 'savePillar'])->name('work.pillars.store');
 Route::get('work/pillars/{pillar}/edit', [\App\Http\Controllers\CmsWorkController::class, 'editPillar'])->name('work.pillars.edit');
 Route::put('work/pillars/{pillar}', [\App\Http\Controllers\CmsWorkController::class, 'savePillar'])->name('work.pillars.update');
 Route::delete('work/pillars/{pillar}', [\App\Http\Controllers\CmsWorkController::class, 'destroyPillar'])->name('work.pillars.destroy');
 Route::get('work/pillars/{pillar}/axes/create', [\App\Http\Controllers\CmsWorkController::class, 'editAxis'])->name('work.axes.create');
 Route::post('work/pillars/{pillar}/axes', [\App\Http\Controllers\CmsWorkController::class, 'saveAxis'])->name('work.axes.store');
 Route::get('work/pillars/{pillar}/axes/{axis}/edit', [\App\Http\Controllers\CmsWorkController::class, 'editAxis'])->name('work.axes.edit');
 Route::put('work/pillars/{pillar}/axes/{axis}', [\App\Http\Controllers\CmsWorkController::class, 'saveAxis'])->name('work.axes.update');
 Route::delete('work/pillars/{pillar}/axes/{axis}', [\App\Http\Controllers\CmsWorkController::class, 'destroyAxis'])->name('work.axes.destroy');
 Route::get('about', [\App\Http\Controllers\CmsAboutController::class, 'index'])->name('about');
 Route::put('about/seo', [\App\Http\Controllers\CmsAboutController::class, 'seo'])->name('about.seo');
 Route::get('about/sections/{section}', [\App\Http\Controllers\CmsAboutController::class, 'section'])->name('about.section');
 Route::put('about/sections/{section}', [\App\Http\Controllers\CmsHomeController::class, 'updateSection'])->name('about.section.update');
 Route::get('about/{kind}/create', [\App\Http\Controllers\CmsAboutController::class, 'editRecord'])->name('about.create');
 Route::get('about/{kind}/{id}/edit', [\App\Http\Controllers\CmsAboutController::class, 'editRecord'])->whereNumber('id')->name('about.edit');
 Route::post('about/{kind}', [\App\Http\Controllers\CmsAboutController::class, 'saveRecord'])->name('about.store');
 Route::put('about/{kind}/{id}', [\App\Http\Controllers\CmsAboutController::class, 'saveRecord'])->whereNumber('id')->name('about.update');
 Route::delete('about/{kind}/{id}', [\App\Http\Controllers\CmsAboutController::class, 'destroyRecord'])->whereNumber('id')->name('about.destroy');
 Route::get('titles', [\App\Http\Controllers\CmsTitlesController::class, 'index'])->name('titles');
 Route::get('titles/{page}', [\App\Http\Controllers\CmsTitlesController::class, 'edit'])->name('titles.edit');
 Route::put('titles/{page}', [\App\Http\Controllers\CmsTitlesController::class, 'update'])->name('titles.update');
 Route::get('preview/{page}', [\App\Http\Controllers\CmsTitlesController::class, 'preview'])->name('preview');
});

Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/home')->name('admin.cms.home')->group(function () {
 Route::get('/', [\App\Http\Controllers\CmsHomeController::class, 'edit']);
 Route::put('/sections/{section}', [\App\Http\Controllers\CmsHomeController::class, 'updateSection'])->name('.section');
 Route::put('/collections/{kind}/{id}', [\App\Http\Controllers\CmsHomeController::class, 'collection'])->name('.collection');
 Route::put('/contact', [\App\Http\Controllers\CmsHomeController::class, 'contact'])->name('.contact');
 Route::put('/seo', [\App\Http\Controllers\CmsHomeController::class, 'updateSeo'])->name('.seo');
 Route::post('/indicators/{indicator}', [\App\Http\Controllers\CmsHomeController::class, 'indicator'])->name('.indicator');
});

Route::middleware(['auth', \App\Http\Middleware\EnsureAdministrator::class])->prefix('admin/cms/sections')->name('admin.cms.sections.')->group(function () {
 Route::get('/', [\App\Http\Controllers\CmsSectionContentController::class,'index'])->name('index');
 Route::get('/{section}', [\App\Http\Controllers\CmsSectionContentController::class,'section'])->name('edit');
 Route::post('/{section}/indicators', [\App\Http\Controllers\CmsSectionContentController::class,'createIndicator'])->name('indicator.create');
 Route::get('/{section}/contents/create', [\App\Http\Controllers\CmsSectionContentController::class,'edit'])->name('create');
 Route::get('/{section}/contents/{content}/edit', [\App\Http\Controllers\CmsSectionContentController::class,'edit'])->name('content.edit');
 Route::post('/{section}/contents', [\App\Http\Controllers\CmsSectionContentController::class,'save'])->name('store');
 Route::put('/{section}/contents/{content}', [\App\Http\Controllers\CmsSectionContentController::class,'save'])->name('update');
 Route::delete('/{section}/contents/{content}', [\App\Http\Controllers\CmsSectionContentController::class,'destroy'])->name('destroy');
});
