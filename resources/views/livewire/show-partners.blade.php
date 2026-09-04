<section id="clients" class="clients section">
    <div class="container" data-aos="fade-up">
        <div class="clients-wrapper">
            <div class="clients-track">
                @foreach($partners as $partner)
                <div class="client-logo">
                    <img src="{{ media_url($partner->logo) }}" class="img-fluid" alt="{{ $partner->name }}" title="{{ $partner->name }}" loading="lazy">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>