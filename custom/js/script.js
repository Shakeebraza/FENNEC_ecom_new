

  document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.car-vhcl-menu-res').forEach(function(item) {
      item.addEventListener('click', function() {
        var categoryId = this.getAttribute('data-id');
        var submenu = document.querySelector('.remenu-main-dw[data-id="' + categoryId + '"]');
        let remenuIn = document.querySelector(".remenu-sub");
        console.log('click');

        if (submenu) {
          document.querySelectorAll('.remenu-main-dw').forEach(function(menu) {
            remenuIn.style.display = 'none';
            menu.style.display = 'none';
          });

          if (submenu.style.display === 'none' || submenu.style.display === '') {
            remenuIn.style.display = 'block';
            submenu.style.display = 'block';
          } else {
            submenu.style.display = 'none';
            remenuIn.style.display = 'none';
          }
        }
      });
    });

    document.querySelectorAll('.crs-end').forEach(function(closeBtn) {
      closeBtn.addEventListener('click', function() {
        let submenu = this.closest('.remenu-main-dw'); 
        let remenuIn = document.querySelector(".remenu-sub");

        submenu.style.display = 'none';
        remenuIn.style.display = 'none';
      });
    });
});


