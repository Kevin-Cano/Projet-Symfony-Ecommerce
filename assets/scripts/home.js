// Navbar scroll effect
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Add/remove visible class based on scroll position
        if (scrollTop > 50) {
            navbar.classList.add('visible');
        } else {
            navbar.classList.remove('visible');
        }
        
        lastScrollTop = scrollTop;
    });
});

// Products slider
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.products-container');
    const cards = document.querySelectorAll('.product-card');
    
    if (!container || cards.length === 0) return;

    // Clone first and last sets of cards
    const firstCards = Array.from(cards).slice(0, 3);
    const lastCards = Array.from(cards).slice(-3);
    
    firstCards.forEach(card => {
        const clone = card.cloneNode(true);
        clone.classList.add('clone');
        container.appendChild(clone);
    });
    
    lastCards.forEach(card => {
        const clone = card.cloneNode(true);
        clone.classList.add('clone');
        container.insertBefore(clone, container.firstChild);
    });

    let currentIndex = 0;
    const cardWidth = cards[0].offsetWidth;
    const gap = 32; // 2rem gap
    const slideWidth = cardWidth + gap;
    
    // Set initial position
    container.style.transform = `translateX(-${slideWidth * 3}px)`;

    // Auto slide
    let autoSlideInterval = setInterval(slideNext, 5000);

    // Navigation buttons
    const prevBtn = document.querySelector('.slider-nav.prev');
    const nextBtn = document.querySelector('.slider-nav.next');

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            clearInterval(autoSlideInterval);
            slidePrev();
            autoSlideInterval = setInterval(slideNext, 5000);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            clearInterval(autoSlideInterval);
            slideNext();
            autoSlideInterval = setInterval(slideNext, 5000);
        });
    }

    function slideNext() {
        currentIndex++;
        slide();
    }

    function slidePrev() {
        currentIndex--;
        slide();
    }

    function slide() {
        container.style.transition = 'transform 0.5s ease-in-out';
        container.style.transform = `translateX(-${(currentIndex + 3) * slideWidth}px)`;

        // Reset to original position when reaching the end
        if (currentIndex >= cards.length) {
            setTimeout(() => {
                container.style.transition = 'none';
                currentIndex = 0;
                container.style.transform = `translateX(-${3 * slideWidth}px)`;
            }, 500);
        }
        // Reset to original position when reaching the start
        else if (currentIndex < 0) {
            setTimeout(() => {
                container.style.transition = 'none';
                currentIndex = cards.length - 1;
                container.style.transform = `translateX(-${(currentIndex + 3) * slideWidth}px)`;
            }, 500);
        }
    }
});