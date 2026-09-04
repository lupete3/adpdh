<section id="skills" class="skills section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-center">
            <div class="col-lg-5">
                <div class="skills-header">
                    <h3>{{ $skillHeader?->title ?? 'Notre Expertise Métier' }}</h3>
                    <p>
                        {{ $skillHeader?->description ?? 'Un savoir-faire multidisciplinaire au service du développement communautaire et de l\'autonomisation des populations vulnérables en RDC.' }}
                    </p>
                    @if($skillHeader?->certifications)
                    <div class="certifications">
                        @foreach($skillHeader->certifications as $cert)
                        <div class="cert-item" data-aos="fade-right" data-aos-delay="{{ 200 + ($loop->index * 50) }}">
                            <i class="bi bi-award-fill"></i>
                            <span>{{ $cert }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-7">
                <div class="skills-grid skills-animation" data-aos="fade-left" data-aos-delay="200">
                    @forelse($skills as $skill)
                    <div class="skill-item">
                        <div class="skill-header">
                            <h4>{{ $skill->title }}</h4>
                            <span class="skill-value">{{ $skill->percentage }}%</span>
                        </div>
                        <div class="skill-bar progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="{{ $skill->percentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $skill->percentage }}%"></div>
                        </div>
                        @if($skill->description)
                        <p>{{ $skill->description }}</p>
                        @endif
                    </div><!-- End Skills Item -->
                    @empty
                    <!-- Fallback skills for Wezesha -->
                    <div class="skill-item">
                        <div class="skill-header">
                            <h4>Éducation & Parrainage</h4>
                            <span class="skill-value">95%</span>
                        </div>
                        <div class="skill-bar progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100" style="width: 95%"></div>
                        </div>
                        <p>Taux de réussite scolaire des enfants parrainés.</p>
                    </div>
                    <div class="skill-item">
                        <div class="skill-header">
                            <h4>Sécurité Alimentaire</h4>
                            <span class="skill-value">85%</span>
                        </div>
                        <div class="skill-bar progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 85%"></div>
                        </div>
                        <p>Augmentation moyenne des récoltes des familles accompagnées.</p>
                    </div>
                    <div class="skill-item">
                        <div class="skill-header">
                            <h4>Autonomisation</h4>
                            <span class="skill-value">90%</span>
                        </div>
                        <div class="skill-bar progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%"></div>
                        </div>
                        <p>Taux de remboursement des micro-crédits solidaires.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
