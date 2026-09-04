<div>
    <x-page-header :title="$achievement->title"/>

    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-8">
                    <!-- Achievement Detail Start -->
                    <div class="mb-5">
                        <img class="img-fluid w-100 rounded mb-5" src="{{ asset('storage/' . $achievement->image) }}" alt="{{ $achievement->title }}">
                        <h1 class="mb-4">{{ $achievement->title }}</h1>
                        <div class="d-flex mb-3">
                            <small class="me-3"><i class="far fa-user text-primary me-2"></i>{{ $achievement->partner->name }}</small>
                            <small class="me-3"><i class="far fa-calendar-alt text-primary me-2"></i>{{ \Carbon\Carbon::parse($achievement->date)->format('d M, Y') }}</small>
                            <small><i class="fa fa-map-marker-alt text-primary me-2"></i>{{ $achievement->location }}</small>
                        </div>
                        <p>{!! nl2br(e($achievement->description)) !!}</p>
                    </div>
                    <!-- Achievement Detail End -->
                </div>

                <!-- Sidebar Start -->
                <div class="col-lg-4">
                    <!-- Recent Achievements Start -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0">Autres Réalisations</h3>
                        </div>
                        @foreach(\App\Models\Achievement::where('id', '!=', $achievement->id)->latest('date')->take(5)->get() as $recentAchievement)
                        <div class="d-flex rounded overflow-hidden mb-3">
                            <img class="img-fluid" src="{{ asset('storage/'. $recentAchievement->image) }}" style="width: 100px; height: 100px; object-fit: cover;" alt="">
                            <a href="{{ route('achievement.detail', $recentAchievement->id) }}" class="h5 fw-semi-bold d-flex align-items-center bg-light px-3 mb-0">{{ $recentAchievement->title }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <!-- Recent Achievements End -->
                </div>
                <!-- Sidebar End -->
            </div>
        </div>
    </div>
</div>
