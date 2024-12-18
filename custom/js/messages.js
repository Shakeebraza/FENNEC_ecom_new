document.addEventListener("DOMContentLoaded", function () {
  const editContactDetailsBtn = document.getElementById("editContactDetailsBtn");
  const editPasswordBtn = document.getElementById("editPasswordBtn");
  
  // Update the contactFields to match your HTML input IDs
  const contactFields = ["country", "city", "contactNumber"];
  const passwordField = document.getElementById("password");




  // Image preview functionality
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
  if (window.location.hash === '#messages543') {
      document.getElementById('messages543').style.display = 'block';
  } else {
      document.getElementById('messages543').style.display = 'none';
  }
};
document.addEventListener('DOMContentLoaded', function() {
  const messagesTab = document.getElementById('messages-tab');
  const messagesContent = document.getElementById('messages543');
  if (window.location.hash === '#messages543') {
      messagesTab.classList.add('active');
      messagesContent.style.display = 'block';
  } else {
      messagesContent.style.display = 'none';
  }
  $('#myTab').on('shown.bs.tab', function (e) {
      if (e.target.getAttribute('data-bs-target') === '#messages543') {
          messagesContent.style.display = 'block';
      } else {
          messagesContent.style.display = 'none';  
      }
  });
});
