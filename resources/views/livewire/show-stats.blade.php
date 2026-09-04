<section id="stats" class="stats section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="stats-board">
                    @foreach($stats as $stat)
                    <article class="stat-tile" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 + 100 }}">
                        <div class="tile-head">
                            <i class="{{ $stat->icon ?? 'bi bi-emoji-smile' }}"></i>
                            <div class="label">
                                <h6 class="title">{{ $stat->title }}</h6>
                                @if($stat->description)
                                <small class="hint">{{ $stat->description }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="tile-metric">
                            <span class="metric-number purecounter" data-purecounter-start="0" data-purecounter-end="{{ intval($stat->value) }}" data-purecounter-duration="1"></span>
                            @if(strpos($stat->value, '+') !== false || strpos($stat->value, '%') !== false)
                            <span class="metric-suffix">{{ substr($stat->value, -1) }}</span>
                            @endif
                        </div>
                    </article><!-- End Stat Tile -->
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</section>
