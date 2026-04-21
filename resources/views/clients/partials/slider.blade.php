    <!-- -----------------------SLIDER---------------------------------------------- -->
    <section class="sliders">
        <div class="aspect-ratio-169 sliders-img">
            @foreach(['silde1','slide2','slide3','slide4','slide5'] as $i => $slide)
            <img src="{{ asset('assets/clients/image/'.$slide.'.jpg') }}" class="slider-image {{ $i === 0 ? 'active' : '' }}">
            @endforeach
        </div>
        <div class="dot-container">
            @for($i = 0; $i < 5; $i++) <div class="dot {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}">
        </div>
        @endfor
        </div>
        <div class="slider-nav">
            <button class="slider-btn prev-btn" onclick="changeSlide(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="slider-btn next-btn" onclick="changeSlide(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>
    {{-- Slider JS giữ nguyên, chỉ copy từ slider.php cũ --}}
    <script src="{{ asset('assets/clients/js/slider.js') }}"></script>
    <script>
let currentSlide = 0;
const slides = document.querySelectorAll('.slider-image');
const dots = document.querySelectorAll('.dot');
const totalSlides = slides.length;

function showSlide(index) {
    // Hide all slides
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    // Show current slide
    slides[index].classList.add('active');
    dots[index].classList.add('active');
}

function changeSlide(direction) {
    currentSlide += direction;

    if (currentSlide >= totalSlides) {
        currentSlide = 0;
    } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    }

    showSlide(currentSlide);
}

// Dot navigation
dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        currentSlide = index;
        showSlide(currentSlide);
    });
});

// Auto slide
let autoSlideInterval = setInterval(() => {
    changeSlide(1);
}, 5000);

// Pause auto slide on hover
const sliderContainer = document.querySelector('.sliders');
sliderContainer.addEventListener('mouseenter', () => {
    clearInterval(autoSlideInterval);
});

sliderContainer.addEventListener('mouseleave', () => {
    autoSlideInterval = setInterval(() => {
        changeSlide(1);
    }, 5000);
});
    </script>
    <!-- -----------------------------------slider----------------------- -->