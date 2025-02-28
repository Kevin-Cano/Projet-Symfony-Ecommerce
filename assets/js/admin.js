document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 100) {
            navbar.classList.add('visible');
        } else {
            navbar.classList.remove('visible');
        }
        
        lastScrollTop = scrollTop;
    });
}); 