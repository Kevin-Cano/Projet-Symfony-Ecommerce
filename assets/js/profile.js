document.addEventListener('DOMContentLoaded', function() {
    function showSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.info-section').forEach(function(section) {
            section.style.display = 'none';
        });

        // Show the selected section
        document.getElementById(sectionId).style.display = 'block';
    }

    // Attach event listeners to buttons
    document.querySelector('button[data-section="personal-info"]').addEventListener('click', function() {
        showSection('personal-info');
    });

    document.querySelector('button[data-section="funds-info"]').addEventListener('click', function() {
        showSection('funds-info');
    });

    document.querySelector('button[data-section="security-info"]').addEventListener('click', function() {
        showSection('security-info');
    });

    // Add event listeners for actions within sections
    document.querySelector('button[data-action="add-card"]').addEventListener('click', function() {
        const cardNumber = prompt('Enter your card number:');
        const cardExpiry = prompt('Enter your card expiry date (MM/YY):');
        const cardCVC = prompt('Enter your card CVC:');

        if (cardNumber && cardExpiry && cardCVC && /^[0-9]{16}$/.test(cardNumber)) {
            alert('Card added successfully!');
            // Here you would typically send the card details to the server
        } else {
            alert('Please enter valid card details.');
        }
    });

    document.querySelector('button[data-action="add-funds"]').addEventListener('click', function() {
        const amount = prompt('Enter the amount to add:');
        if (amount && !isNaN(amount)) {
            fetch('/account/add-funds', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `amount=${amount}`
            })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        } else {
            alert('Please enter a valid amount.');
        }
    });

    document.querySelector('button[data-action="update-phone"]').addEventListener('click', function() {
        const phoneNumber = prompt('Enter your new phone number:');
        if (phoneNumber && /^\+?[0-9]{10,15}$/.test(phoneNumber)) {
            fetch('/account/update-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `phoneNumber=${phoneNumber}`
            })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        } else {
            alert('Please enter a valid phone number.');
        }
    });

    document.querySelector('button[data-action="update-password"]').addEventListener('click', function() {
        const newPassword = prompt('Enter your new password:');
        const confirmPassword = prompt('Confirm your new password:');
        if (newPassword && confirmPassword && newPassword === confirmPassword) {
            fetch('/account/update-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `newPassword=${newPassword}&confirmPassword=${confirmPassword}`
            })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        } else {
            alert('Passwords do not match or are empty.');
        }
    });

    document.querySelector('button[data-action="delete-account"]').addEventListener('click', function() {
        const confirmation = confirm('Are you sure you want to delete your account? This action cannot be undone.');
        if (confirmation) {
            fetch('/account/delete-account', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    window.location.href = '/';
                })
                .catch(error => console.error('Error:', error));
        }
    });
});