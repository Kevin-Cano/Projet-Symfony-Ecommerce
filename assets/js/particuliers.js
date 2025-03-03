document.addEventListener('DOMContentLoaded', function() {
    // Gestion du tri
    const sortSelect = document.getElementById('sort-by');
    const watchesContainer = document.getElementById('watches-container');
    const watches = Array.from(document.querySelectorAll('.watch-card'));
    
    sortSelect.addEventListener('change', function() {
        const sortValue = this.value;
        
        // Trier les montres
        watches.sort(function(a, b) {
            const priceA = parseFloat(a.dataset.price);
            const priceB = parseFloat(b.dataset.price);
            
            if (sortValue === 'price-asc') {
                return priceA - priceB;
            } else if (sortValue === 'price-desc') {
                return priceB - priceA;
            } else if (sortValue === 'newest') {
                // Pour simplifier, on utilise l'ordre initial
                return Array.from(watchesContainer.children).indexOf(a) - 
                       Array.from(watchesContainer.children).indexOf(b);
            }
        });
        
        // Réorganiser les montres dans le DOM
        watches.forEach(function(watch) {
            watchesContainer.appendChild(watch);
        });
    });
    
    // Gestion du filtre de prix
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');
    
    priceRange.addEventListener('input', function() {
        const maxPrice = parseInt(this.value);
        priceValue.textContent = maxPrice.toLocaleString('fr-FR') + ' €';
        
        // Filtrer les montres
        watches.forEach(function(watch) {
            const price = parseFloat(watch.dataset.price);
            if (price <= maxPrice) {
                watch.style.display = 'block';
            } else {
                watch.style.display = 'none';
            }
        });
    });
});