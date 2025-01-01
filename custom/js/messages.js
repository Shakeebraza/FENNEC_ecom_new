document.addEventListener("DOMContentLoaded", function () {
  const editContactDetailsBtn = document.getElementById("editContactDetailsBtn");
  const editPasswordBtn = document.getElementById("editPasswordBtn");
  

  const contactFields = ["country", "city", "contactNumber"];
  const passwordField = document.getElementById("password");





  const imageInput = document.getElementById("images");
  const imagePreview = document.getElementById("imagePreview");

  if (imageInput) {
    imageInput.addEventListener("change", function (event) {
      imagePreview.innerHTML = "";
      const files = event.target.files;
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type.startsWith("image/")) {
          const img = document.createElement("img");
          img.src = URL.createObjectURL(file);
          img.onload = function () {
            URL.revokeObjectURL(this.src);
          };
          imagePreview.appendChild(img);
        }
      }
    });
  }
});

window.onload = function() {
  if (window.location.hash === '#Messages') {
      document.getElementById('Messages').style.display = 'block';
  } else {
      document.getElementById('Messages').style.display = 'none';
  }
};
document.addEventListener('DOMContentLoaded', function() {
  const messagesTab = document.getElementById('messages-tab');
  const messagesContent = document.getElementById('Messages');


  if (window.location.hash === '#Messages') {
      messagesTab.classList.add('active');
      messagesContent.classList.add('show', 'active');
  } else {
      messagesContent.classList.remove('show', 'active');
  }


  const myTab = document.querySelector('#myTab');
  myTab.addEventListener('shown.bs.tab', function(e) {
      if (e.target.getAttribute('data-bs-target') === '#Messages') {
          messagesContent.classList.add('show', 'active');
      } else {
          messagesContent.classList.remove('show', 'active');
      }
  });
});

