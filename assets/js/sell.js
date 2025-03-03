document.addEventListener('DOMContentLoaded', function() {
    // Prévisualisation de l'image
    const imageInput = document.getElementById('watch_picture');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.add('has-image');
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '#';
            imagePreview.classList.remove('has-image');
        }
    });
    
    // Clic sur le bouton de parcourir
    document.querySelector('.upload-button').addEventListener('click', function(e) {
        e.preventDefault();
        imageInput.click();
    });
    
    // Glisser-déposer
    imagePreview.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#2D3C4C';
    });
    
    imagePreview.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ced4da';
    });
    
    imagePreview.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ced4da';
        
        const file = e.dataTransfer.files[0];
        if (file && file.type.match('image.*')) {
            imageInput.files = e.dataTransfer.files;
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.add('has-image');
            }
            reader.readAsDataURL(file);
        }
    });
    
    // FAQ accordéon
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            faqItem.classList.toggle('active');
        });
    });
});