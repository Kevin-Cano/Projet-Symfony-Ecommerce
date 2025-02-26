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
});