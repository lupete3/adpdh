<section id="team" class="team section">
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ $header?->title ?? 'Notre Équipe' }}</h2>
        <p>{{ $header?->subtitle ?? 'Des cœurs dévoués au service de l\'humanité' }}</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-4 align-items-stretch">
            @foreach($teamMembers as $member)
            <div class="col-md-6 col-lg-3">
                <article class="member-card h-100" data-aos="zoom-in" data-aos-delay="{{ 100 + ($loop->iteration * 50) }}">
                    <figure class="member-media">
                        <img src="{{ media_url($member->photo) }}" class="img-fluid" alt="{{ $member->name }}">
                        @if($member->twitter_url || $member->linkedin_url || $member->facebook_url)
                        <ul class="social-list">
                            @if($member->twitter_url)<li><a href="{{ $member->twitter_url }}" aria-label="Twitter"><i class="bi bi-twitter"></i></a></li>@endif
                            @if($member->linkedin_url)<li><a href="{{ $member->linkedin_url }}" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a></li>@endif
                            @if($member->facebook_url)<li><a href="{{ $member->facebook_url }}" aria-label="Facebook"><i class="bi bi-facebook"></i></a></li>@endif
                        </ul>
                        @endif
                        <div class="member-overlay">
                            <div class="overlay-content">
                                <p>{{ $member->description }}</p>
                            </div>
                        </div>
                    </figure>
                    <div class="member-content">
                        <h3 class="member-name">{{ $member->name }}</h3>
                        <p class="member-role">{{ $member->position }}</p>
                    </div>
                </article>
            </div>
            @endforeach
        </div>
    </div>
</section>