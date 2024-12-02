document.addEventListener('DOMContentLoaded', function() {
    const viewOptions = document.querySelectorAll('.view-option');
    const productGrid = document.getElementById('product-grid');

    viewOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            viewOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to clicked option
            this.classList.add('active');

            // Get the number of columns from the data-cols attribute
            const cols = this.getAttribute('data-cols');

            // Remove all existing column classes
            productGrid.className = 'row g-4';

            // Add new column classes based on the selected option
            if (cols === '1') {
                productGrid.classList.add('row-cols-1');
            } else {
                productGrid.classList.add('row-cols-1', `row-cols-md-${cols}`);
            }

            // Special handling for list view
            if (cols === '1') {
                productGrid.querySelectorAll('.product-card').forEach(card => {
                    card.classList.add('d-flex', 'flex-row');
                    card.querySelector('.card-img-top').style.width = '200px';
                    card.querySelector('.card-body').classList.add('flex-grow-1');
                });
            } else {
                productGrid.querySelectorAll('.product-card').forEach(card => {
                    card.classList.remove('d-flex', 'flex-row');
                    card.querySelector('.card-img-top').style.width = '100%';
                    card.querySelector('.card-body').classList.remove('flex-grow-1');
                });
            }
        });
    });
});