document.addEventListener('DOMContentLoaded', function() {
    // Navbar scroll
    const navbar = document.querySelector('.navbar');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        if (currentScroll > 50) {
            navbar.classList.add('visible');
        } else {
            navbar.classList.remove('visible');
        }
        lastScroll = currentScroll;
    });

    // Infinite slider
    const container = document.querySelector('.products-container');
    const prevBtn = document.querySelector('.slider-nav.prev');
    const nextBtn = document.querySelector('.slider-nav.next');
    const cards = document.querySelectorAll('.product-card');

    // Clone first and last sets of cards
    const firstCards = Array.from(cards).slice(0, 3);
    const lastCards = Array.from(cards).slice(-3);

    // Add clones to the container
    lastCards.forEach(card => {
        const clone = card.cloneNode(true);
        clone.classList.add('clone');
        container.insertBefore(clone, container.firstChild);
    });

    firstCards.forEach(card => {
        const clone = card.cloneNode(true);
        clone.classList.add('clone');
        container.appendChild(clone);
    });

    let currentIndex = 1; // Start at 1 to account for cloned cards
    let isTransitioning = false;

    function updateSlider(smooth = true) {
        const slideWidth = cards[0].offsetWidth + 32; // width + gap
        container.style.transition = smooth ? 'transform 0.5s ease' : 'none';
        container.style.transform = `translateX(-${currentIndex * slideWidth * 3}px)`;
    }

    function slideNext() {
        if (isTransitioning) return;
        isTransitioning = true;
        currentIndex++;
        updateSlider();
    }

    function slidePrev() {
        if (isTransitioning) return;
        isTransitioning = true;
        currentIndex--;
        updateSlider();
    }

    // Handle infinite scroll transitions
    container.addEventListener('transitionend', () => {
        isTransitioning = false;
        const totalSets = cards.length / 3;

        if (currentIndex >= totalSets + 1) {
            currentIndex = 1;
            updateSlider(false);
        }

        if (currentIndex <= 0) {
            currentIndex = totalSets;
            updateSlider(false);
        }
    });

    // Initialize slider position
    updateSlider(false);

    // Event listeners for navigation
    prevBtn.addEventListener('click', slidePrev);
    nextBtn.addEventListener('click', slideNext);

    // Auto slide every 5 seconds
    let autoSlideInterval = setInterval(slideNext, 5000);

    // Pause auto slide on hover
    container.addEventListener('mouseenter', () => {
        clearInterval(autoSlideInterval);
    });

    container.addEventListener('mouseleave', () => {
        autoSlideInterval = setInterval(slideNext, 5000);
    });

    var carousel = new bootstrap.Carousel(document.getElementById('watchCarousel'), {
        interval: 3000,
        wrap: true
    });
});