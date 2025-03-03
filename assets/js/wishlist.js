document.addEventListener('DOMContentLoaded', function() {
    // Gestion des petits boutons cœur sur les cartes
    const smallHeartButtons = document.querySelectorAll('.heart-button-small');
    
    smallHeartButtons.forEach(button => {
        const watchId = button.getAttribute('data-id');
        const icon = button.querySelector('.heart-icon');
        
        // Vérifier si la montre est déjà dans la liste de souhaits
        fetch(`/wishlist/check/${watchId}`)
            .then(response => response.json())
            .then(data => {
                if (data.inWishlist) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    button.classList.add('in-wishlist');
                }
            });
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (button.classList.contains('in-wishlist')) {
                // Retirer de la liste de souhaits
                fetch(`/wishlist/remove/${watchId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        button.classList.remove('in-wishlist');
                        
                        // Notification
                        showNotification('Montre retirée de votre liste de souhaits', 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                });
            } else {
                // Ajouter à la liste de souhaits
                fetch(`/wishlist/add/${watchId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        button.classList.add('in-wishlist');
                        
                        // Notification
                        showNotification('Montre ajoutée à votre liste de souhaits', 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                });
            }
        });
    });
});

// Fonction pour afficher une notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.add('show');
        
        // Animation de sortie après 3 secondes
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }, 10);
}

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons de suppression
    const removeButtons = document.querySelectorAll('.btn-remove');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const watchId = this.getAttribute('data-id');
            const itemElement = this.closest('.wishlist-item');
            
            fetch(`/wishlist/remove/${watchId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Animation de suppression
                    itemElement.style.opacity = '0';
                    setTimeout(() => {
                        itemElement.style.height = '0';
                        itemElement.style.margin = '0';
                        itemElement.style.padding = '0';
                        itemElement.style.overflow = 'hidden';
                        
                        setTimeout(() => {
                            itemElement.remove();
                            
                            // Vérifier s'il reste des éléments
                            const remainingItems = document.querySelectorAll('.wishlist-item');
                            if (remainingItems.length === 0) {
                                // Afficher le message "liste vide"
                                const wishlistItems = document.querySelector('.wishlist-items');
                                wishlistItems.innerHTML = `
                                    <div class="empty-wishlist">
                                        <p>Votre liste de souhaits est vide</p>
                                        <a href="{{ path('app_collection') }}" class="btn-continue">Découvrir notre collection</a>
                                    </div>
                                `;
                            }
                        }, 300);
                    }, 300);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});