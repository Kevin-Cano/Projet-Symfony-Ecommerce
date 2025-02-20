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

    // Slider infini manuel
    const container = document.querySelector('.products-container');
    const prevBtn = document.querySelector('.slider-nav.prev');
    const nextBtn = document.querySelector('.slider-nav.next');
    const cards = document.querySelectorAll('.product-card');

    if (container && cards.length > 0) {
        // Créer suffisamment de clones pour un défilement continu
        const totalClones = 2;
        for (let i = 0; i < totalClones; i++) {
            cards.forEach(card => {
                const clone = card.cloneNode(true);
                clone.classList.add('clone');
                container.appendChild(clone);
            });
        }

        let currentPosition = 0;
        let isTransitioning = false;

        function updatePosition(smooth = true) {
            const cardWidth = cards[0].offsetWidth + 32;
            container.style.transition = smooth ? 'transform 0.3s ease' : 'none';
            container.style.transform = `translateX(-${currentPosition * cardWidth}px)`;
        }

        function slide(direction) {
            if (isTransitioning) return;
            isTransitioning = true;

            currentPosition += direction;
            updatePosition(true);

            setTimeout(() => {
                isTransitioning = false;
            }, 300);
        }

        // Event listeners
        prevBtn.addEventListener('click', () => slide(-1));
        nextBtn.addEventListener('click', () => slide(1));

        // Position initiale
        updatePosition(false);
    }

    var carousel = new bootstrap.Carousel(document.getElementById('watchCarousel'), {
        interval: 3000,
        wrap: true
    });
});