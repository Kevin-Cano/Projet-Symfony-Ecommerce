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

document.querySelectorAll('.stock-control').forEach(control => {
    const input = control.querySelector('.stock-input');
    const saveButton = control.querySelector('.btn-save-stock');

    // Afficher/masquer le bouton quand la valeur change
    input.addEventListener('input', function() {
        const originalValue = this.dataset.originalValue;
        saveButton.style.display = this.value !== originalValue ? 'block' : 'none';
    });

    saveButton.addEventListener('click', function() {
        const watchId = input.dataset.watchId;
        fetch(`/admin/watch/stock/${watchId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `stock=${input.value}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour la valeur originale
                input.dataset.originalValue = input.value;
                // Cacher le bouton
                saveButton.style.display = 'none';
                // Feedback visuel
                saveButton.style.backgroundColor = '#218838';
                setTimeout(() => {
                    saveButton.style.backgroundColor = '#28a745';
                }, 500);
            }
        })
        .catch(error => {
            alert('Erreur lors de la mise à jour du stock');
        });
    });
});