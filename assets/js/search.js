document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const searchIcon = document.querySelector('.search-icon');
    
    // Fonction pour effectuer la recherche
    function performSearch() {
        const query = searchInput.value.trim();
        if (query.length > 0) {
            window.location.href = `/search?q=${encodeURIComponent(query)}`;
        }
    }
    
    // Événement pour le clic sur l'icône de recherche
    searchIcon.addEventListener('click', function(e) {
        e.preventDefault();
        performSearch();
    });
    
    // Événement pour la touche Entrée dans le champ de recherche
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
}); 