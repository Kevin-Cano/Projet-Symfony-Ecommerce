document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons de section
    const sectionButtons = document.querySelectorAll('.nav-button');
    const sections = document.querySelectorAll('.content-section');
    
    if (sectionButtons.length > 0) {
        sectionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sectionId = this.getAttribute('data-section');
                
                // Masquer toutes les sections
                sections.forEach(section => {
                    section.classList.remove('active');
                });
                
                // Afficher la section sélectionnée
                document.getElementById(sectionId).classList.add('active');
                
                // Mettre à jour la classe active
                sectionButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
    
    // Gestion du formulaire d'ajout de fonds
    const addFundsBtn = document.getElementById('add-funds-btn');
    const amountButtons = document.querySelectorAll('.amount-btn');
    const customAmountInput = document.getElementById('custom-amount');
    const fundsMessage = document.getElementById('funds-message');
    
    // Sélection des montants prédéfinis
    amountButtons.forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            customAmountInput.value = amount;
            
            // Mettre à jour la classe active
            amountButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Soumission du formulaire d'ajout de fonds
    if (addFundsBtn) {
        addFundsBtn.addEventListener('click', function() {
            const amount = customAmountInput.value;
            
            if (!amount || isNaN(amount) || amount <= 0) {
                showMessage(fundsMessage, 'Veuillez entrer un montant valide.', 'error');
                return;
            }
            
            fetch('/account/add-funds', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `amount=${amount}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour l'affichage du solde
                    const balanceElement = document.querySelector('.current-balance');
                    if (balanceElement) {
                        balanceElement.textContent = `${data.newBalance.toLocaleString('fr-FR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })} €`;
                    }
                    
                    // Mettre à jour également le solde dans la section des infos personnelles
                    const statValue = document.querySelector('.stat-value:nth-of-type(2)');
                    if (statValue) {
                        statValue.textContent = `${data.newBalance.toLocaleString('fr-FR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })} €`;
                    }
                    
                    showMessage(fundsMessage, data.message, 'success');
                    customAmountInput.value = '';
                    amountButtons.forEach(btn => btn.classList.remove('active'));
                } else {
                    showMessage(fundsMessage, data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage(fundsMessage, 'Une erreur est survenue.', 'error');
            });
        });
    }
    
    // Gestion des boutons d'édition des informations personnelles
    const editButtons = document.querySelectorAll('.edit-btn');
    const personalInfoMessage = document.getElementById('personal-info-message');
    
    if (editButtons.length > 0) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const field = this.getAttribute('data-field');
                const input = document.getElementById(field);
                const value = input.value;
                
                if (!value) {
                    showMessage(personalInfoMessage, 'Ce champ ne peut pas être vide.', 'error');
                    return;
                }
                
                // Envoi de la requête AJAX
                fetch('/account/update-personal-info', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `field=${field}&value=${value}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(personalInfoMessage, data.message, 'success');
                    } else {
                        showMessage(personalInfoMessage, data.message || 'Une erreur est survenue.', 'error');
                    }
                })
                .catch(error => {
                    showMessage(personalInfoMessage, 'Une erreur est survenue lors de la communication avec le serveur.', 'error');
                    console.error('Error:', error);
                });
            });
        });
    }
    
    // Fonction pour afficher les messages
    function showMessage(element, message, type) {
        if (!element) return;
        
        element.innerHTML = `<div class="message ${type}">${message}</div>`;
        element.style.display = 'block';
        
        setTimeout(() => {
            element.style.display = 'none';
        }, 5000);
    }
}); 