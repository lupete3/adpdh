<div>
@if($sliders->count() > 0)
    <!-- Hero NGO Slider Section -->
    <section id="hero" class="hero-ngo-slider section dark-background">
      <div class="swiper init-swiper" x-data x-init="
        const config = JSON.parse($el.querySelector('.swiper-config').innerHTML);
        const swiper = new Swiper($el, config);
        swiper.on('slideChangeTransitionStart', function() {
            if (typeof aosInit === 'function') { aosInit(); }
        });
      ">
        <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "speed": 1000,
            "autoplay": {
              "delay": 6000,
              "disableOnInteraction": false
            },
            "slidesPerView": 1,
            "effect": "fade",
            "pagination": {
              "el": ".swiper-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": ".swiper-button-next",
              "prevEl": ".swiper-button-prev"
            }
          }
        </script>
        <div class="swiper-wrapper">
          @foreach($sliders as $slider)
          <div class="swiper-slide">
            <div class="slider-bg" style="background-image: url('{{ str_replace('\\', '/', media_url($slider->image)) }}');"></div>
            <div class="container">
              <div class="slider-content">
                <div class="eyebrow" data-aos="fade-down" data-aos-duration="800">
                  <i class="bi bi-stars"></i>
                  <span>{{ $slider->subtitle }}</span>
                </div>
                <h2 data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">{{ $slider->title }}</h2>
                <p class="description" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                  {{ $slider->description ?? 'Promouvoir une éducation de qualité et le bien-être social des orphelins et familles défavorisées en RDC.' }}
                </p>
                <div class="btn-group" data-aos="fade-up" data-aos-delay="600" data-aos-duration="1000">
                  <a href="{{ $slider->button1_url }}" class="btn-brand-orange">
                    <span>{{ $slider->button1_text }}</span>
                    <i class="bi bi-arrow-right ms-2"></i>
                  </a>
                  @if($slider->button2_url)
                  <a href="{{ $slider->button2_url }}" class="btn-outline-white">
                    <i class="bi bi-heart-fill me-2"></i>
                    <span>{{ $slider->button2_text }}</span>
                  </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        
        <!-- Swiper Controls -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      </div>
    </section>
@endif
</div>