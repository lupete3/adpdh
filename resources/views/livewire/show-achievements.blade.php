<section id="stats" class="stats section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="stats-board">
                    @foreach($stats as $stat)
                    <article class="stat-tile" data-aos="fade-up" data-aos-delay="{{ 100 + ($loop->iteration * 50) }}">
                        <div class="tile-head">
                            <i class="{{ $stat->icon ?? 'bi bi-graph-up' }}"></i>
                            <div class="label">
                                <h6 class="title">{{ $stat->title }}</h6>
                                <small class="hint">{{ Str::limit($stat->description, 30) }}</small>
                            </div>
                        </div>
                        <div class="tile-metric">
                            <span class="metric-number purecounter" data-purecounter-start="0" data-purecounter-end="{{ preg_replace('/[^0-9]/', '', $stat->value) }}" data-purecounter-duration="1">{{ preg_replace('/[^0-9]/', '', $stat->value) }}</span>
                            <span class="metric-suffix">{{ preg_replace('/[0-9]/', '', $stat->value) ?: '+' }}</span>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="legend-row" data-aos="fade-down" data-aos-delay="350">
                    <div class="legend-item">
                        <span class="dot dot-primary"></span>
                        <span class="text">{{ $header?->title ?? 'Données mises à jour annuellement' }}</span>
                    </div>
                    <div class="legend-item">
                        <span class="dot dot-neutral"></span>
                        <span class="text">{{ $header?->subtitle ?? 'Comparé aux standards du secteur' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>