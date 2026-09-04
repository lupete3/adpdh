<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
      <div class="w-100 d-flex justify-content-center">
        <div class="text-center w-100">
          @if(isset($settings['logo']) && $settings['logo']->value)
            <img src="{{ asset('storage/' . $settings['logo']->value) }}" class="img-fluid mb-4" alt="Logo" style="max-width: 450px;">
          @else
            <img src="{{ asset('flexbiz/assets/img/favicon.png') }}" class="img-fluid mb-4" alt="Logo" style="max-width: 200px;">
          @endif
          <h2 class="fw-bold text-primary">{{ $settings['site_name']->value ?? 'WEZESHA FOUNDATION' }}</h2>
          <p class="text-muted">{{ $settings['slogan']->value ?? '' }}</p>
        </div>
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Right Text -->
    <div class="card col-12 col-lg-5 col-xl-4">
      <div class="d-flex align-items-center authentication-bg p-sm-12 p-6 h-100">
        <div class="w-px-400 mx-auto mt-sm-12 mt-8">
          {{ $slot }}
          <div class="divider my-6">
            <div class="divider-text">or</div>
          </div>

          <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-facebook me-1_5">
              <i class="icon-base bx bxl-facebook-circle icon-20px"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-twitter me-1_5">
              <i class="icon-base bx bxl-twitter icon-20px"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-github me-1_5">
              <i class="icon-base bx bxl-github icon-20px"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-google-plus">
              <i class="icon-base bx bxl-google icon-20px"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- /Right Text -->
  </div>
</div>
