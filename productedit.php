<?php
require_once 'global.php';
include_once 'header.php';
$setSession = $fun->isSessionSet();

if ($setSession == false) {
  $redirectUrl = $urlval . 'index.php';
  echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>';
  exit();
}
$userid = intval(base64_decode($_SESSION['userid'])) ?? 0;
$userData = $dbFunctions->getDatanotenc('user_detail', "userid = '$userid'");

$slug = $_GET['slug'];
$product = $productFun->getProductDetailsBySlugnew($slug);

?>
<style>
  input#gallery {
    height: 50px !important;
    width: 50px !important;
    padding: 60px !important;
    background: url("custom/asset/add-image-icon.ea516b80c0402f99dfb041ba4db057ce\ \(1\).png") no-repeat;
    background-size: contain;
    background-position: center;
    background-color: #ECEDEF;

}

.upld-free-imag {
    display: flex;
    align-items: center;
    justify-content: center;
}

input#gallery {
    -webkit-appearance: none;
    appearance: none;
    opacity: 0;
    position: absolute;
    z-index: -1;
}

.fdfadfbfhfkj {
    padding: 20px;
    background: #ECEDEF;
    border-radius: 10px;
    border: 1px dashed bl;
}

.fdfadfbfhfkj img {
    height: 50px !important;
    width: 50px !important;
}
</style>

<div class="card col-6 mt-5 m-auto mb-5">
  <div class="card-header">
    <h3 class="card-title pb-3">Update Ads</h3>
    <p class="card-subtitle text-muted">
      Update in the details to list your ads for sale.
    </p>
  </div>
  <div class="card-body">
    <form id="productForm" enctype="multipart/form-data">
      <h5>Basic Info</h5>

      <div class="mb-3">
        <label for="title" class="form-label">Product Title</label>
        <input type="text" class="form-control" id="title" name="productName"
          value="<?php echo htmlspecialchars($product['product']['product_name']); ?>"
          placeholder="Enter product title" required />
        <input type="hidden" class="form-control" id="title" name="productId" value="<?php echo $product['product']['product_id']; ?>" />
        <div class="text-danger" id="productNameError"></div>
      </div>


      <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <select class="form-select" id="category" name="category" required>
          <?php
          $findCate = $categoryManager->getAllCategoriesHeaderMenu();
          if ($findCate['status'] == 'success') {
            foreach ($findCate['data'] as $category) {
              $selected = ($category['id'] == $product['product']['category_id']) ? 'selected' : '';
              echo '<option value="' . $category['id'] . '" ' . $selected . '>' . $category['category_name'] . '</option>';
            }
          }
          ?>
        </select>
        <div class="text-danger" id="categoryError"></div>
      </div>

      <?php

      $categoryId = $product['product']['category_id'];
      $selectedSubcategoryId = $product['product']['subcategory_id'];
      $subcategories = $productFun->getSubcategories($categoryId, $selectedSubcategoryId);
      ?>
      <div class="mb-3">
        <label for="subcategory" class="form-label">Subcategory</label>
        <select class="form-select" id="subcategory" name="subcategory" required>
          <option value="" disabled>Select Subcategory</option>
          <?php
          foreach ($subcategories as $subcategory) {
            $selected = $subcategory['selected'] ? 'selected' : '';
            echo '<option value="' . htmlspecialchars($subcategory['id']) . '" ' . $selected . '>'
              . htmlspecialchars($subcategory['name']) . '</option>';
          }
          ?>
        </select>
        <div class="text-danger" id="subcategoryError"></div> 
      </div>

      
      <div class="mb-3">
        <label class="form-label">Condition</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="condition" id="conditionNew" value="new"
            <?php echo ($product['product']['conditions'] == 'new') ? 'checked' : ''; ?> required />
          <label class="form-check-label" for="conditionNew">New</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="condition" id="conditionUsed" value="used"
            <?php echo ($product['product']['conditions'] == 'used') ? 'checked' : ''; ?> required />
          <label class="form-check-label" for="conditionUsed">Used</label>
        </div>
        <div class="text-danger" id="conditionError"></div> 
      </div>

      <!-- Price -->
      <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" class="form-control" name="price" id="price"
          value="<?php echo htmlspecialchars($product['product']['price']); ?>"
          placeholder="Enter price" required />
        <div class="text-danger" id="priceError"></div>
      </div>

      <h5>Details</h5>
   
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" rows="3" name="description"
          placeholder="Describe your product" required><?php echo htmlspecialchars($product['product']['product_description']); ?></textarea>
        <div class="text-danger" id="descriptionError"></div> 
      </div>

      <div class="mb-3">
        <label for="brand" class="form-label">Brand</label>
        <input type="text" class="form-control" id="brand" name="brand"
          value="<?php echo htmlspecialchars($product['product']['brand']); ?>"
          placeholder="Enter brand name" required />
        <div class="text-danger" id="brandError"></div>
      </div>


      <div class="mb-3">
        <label for="country" class="form-label">Country</label>
        <select id="country" name="country" class="form-select" required>
          <option value="" disabled>Select a country</option>
          <?php
          $countries = $dbFunctions->getData('countries');
          foreach ($countries as $cont) {
            $countryId = $security->decrypt($cont['id']);
            $selected = ($countryId == $product['product']['country_id']) ? 'selected' : '';
            echo '<option value="' . $countryId . '" ' . $selected . '>' . $security->decrypt($cont['name']) . '</option>';
          }
          ?>
        </select>
        <div class="text-danger" id="countryError"></div> 
      </div>

      <?php
      $countryId = $product['product']['country_id']; 
      $selectedCityId = $product['product']['city_id']; 
      $cities = $productFun->getCities($countryId, $selectedCityId);
      ?>

      <div class="mb-3">
        <label for="city" class="form-label">City</label>
        <select class="form-select" id="city" name="city" required>
          <option value="" disabled>Select City</option>
          <?php
          foreach ($cities as $city) {
            $selected = $city['selected'] ? 'selected' : '';
            echo '<option value="' . htmlspecialchars($city['id']) . '" ' . $selected . '>'
              . htmlspecialchars($city['name']) . '</option>';
          }
          ?>
        </select>
        <div class="text-danger" id="cityError"></div> 
      </div>


      <h5>Media</h5>
      <?php

      if (!empty($product['gallery_images'])) {
        usort($product['gallery_images'], function ($a, $b) {
          return $a['sort'] <=> $b['sort'];
        });
      }
      ?>

      <div
        class="image-upload-section"
        style="
        border: 1px solid #ddd; 
        border-radius: 8px; 
        padding: 20px; 
        margin-top: 20px; 
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    ">
        <h5
          style="
            margin-bottom: 15px; 
            font-size: 18px; 
            font-weight: bold; 
            color: #333;
        ">
          Drag to Sort Your Images
        </h5>
        <p
          style="
            font-size: 14px; 
            color: #555; 
            margin-bottom: 15px; 
            line-height: 1.6;
        ">
          You can drag and reorder the images to set their display order. The first image will be used as the primary product image.
        </p>
        <div
          id="imagePreview"
          class="upload-preview"
          style="
            display: flex; 
            flex-wrap: wrap; 
            gap: 15px;
            align-items: flex-start;
        ">
          <?php
          if (!empty($product['gallery_images'])) {
            foreach ($product['gallery_images'] as $key => $image) {
              echo '
                <div 
                    class="image-card" 
                    style="
                        position: relative; 
                        width: 120px; 
                        height: 120px; 
                        overflow: hidden; 
                        border: 1px solid #ccc; 
                        border-radius: 8px; 
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
                        cursor: grab;
                    "
                    draggable="true"
                >
                    <img 
                        src="' . htmlspecialchars($image['image_path']) . '" 
                        alt="Product Image" 
                        style="
                            width: 100%; 
                            height: 100%; 
                            object-fit: cover; 
                            border-radius: 8px;
                        " 
                    />
                    <button 
                        type="button" 
                        class="btn btn-danger btn-sm delete-btn" 
                        data-key="' . $image['image_id'] . '" 
                        style="
                            position: absolute; 
                            top: 5px; 
                            right: 5px; 
                            background-color: #dc3545; 
                            color: white; 
                            border: none; 
                            border-radius: 50%; 
                            font-size: 14px; 
                            width: 24px; 
                            height: 24px; 
                            display: flex; 
                            justify-content: center; 
                            align-items: center; 
                            cursor: pointer;
                        ">
                        &times;
                    </button>
                </div>';
            }
          }
          ?>
        </div>
        <div class="form-group mb-3"
                    style="padding: 20px;border: 1px dashed #d8d6d9 ;border-radius: 10px;box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);display: grid;justify-content: center;">
                    <div class="upld-free-imag d-flex w-100">
                        <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                    </div>
                    <label for="gallery" class="custom-file-upload D-FLEX">
                        <div class="fdfadfbfhfkj">
                            <img src="custom/asset/add-image-icon.ea516b80c0402f99dfb041ba4db057ce (1).png" alt="">
                        </div>
                    </label>
                    
                </div>
      </div>

      <button type="submit" class="btn btn-success float-end mt-3">Update Listing</button>
      <button type="button" class="btn btn-secondary float-end mt-3 me-2" onclick="goBack()">Cancel</button>
    </form>
  </div>
</div>




<?php
include_once 'footer.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const imagePreview = document.getElementById('imagePreview');


    imagePreview.addEventListener('click', function(e) {
      if (e.target.classList.contains('delete-btn')) {
        const imageCard = e.target.closest('.image-card');
        const key = e.target.getAttribute('data-key');

        fetch('<?= $urlval ?>ajax/delete_image.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              image_key: key
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // console.log('Image deleted successfully');
              imageCard.remove();
            } else {
              alert('Error deleting image');
            }
          })
          .catch(error => console.error('Error:', error));
      }
    });


    Sortable.create(imagePreview, {
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: function(evt) {

        const updatedOrder = [];
        document.querySelectorAll('.image-card').forEach((card, index) => {
            const key = card.querySelector('.delete-btn').getAttribute('data-key');
            const url = card.querySelector('img').getAttribute('src');  
            updatedOrder.push({
                key: key,
                sort: index,
                url: url  
            });
        });

        fetch('<?= $urlval ?>ajax/update_sort_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order: updatedOrder
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
      
            } else {
                alert('Error updating sort order');
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
});

  $('#productForm').on('submit', function(e) {
    e.preventDefault();


    $('.text-danger').text('');


    var formData = new FormData(this);


    $.ajax({
      url: '<?= $urlval ?>ajax/updateproduct.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.success) {
          $('.btn-success').hide();
          $('.btn-success').after('<div class="alert alert-success mt-3">Ads updated successfully!</div>');
          setTimeout(function() {
                            window.location.href = '<?=$urlval?>success_page.php';
                        }, 1000); 
          showSuccessAlert();
        } else {

          if (response.errors) {
            if (response.errors.productName) {
              $('#productNameError').text(response.errors.productName);
            }
            if (response.errors.image) {
              $('#imageError').text(response.errors.image);
            }
            if (response.errors.description) {
              $('#descriptionError').text(response.errors.description);
            }
            if (response.errors.category) {
              $('#categoryError').text(response.errors.category);
            }
            if (response.errors.subcategory) {
              $('#subcategoryError').text(response.errors.subcategory);
            }
            if (response.errors.brand) {
              $('#brandError').text(response.errors.brand);
            }
            if (response.errors.condition) {
              $('#conditionError').text(response.errors.condition);
            }
            if (response.errors.country) {
              $('#countryError').text(response.errors.country);
            }
            if (response.errors.city) {
              $('#cityError').text(response.errors.city);
            }
            if (response.errors.price) {
              $('#priceError').text(response.errors.price);
            }
            if (response.errors.discountPrice) {
              $('#discountPriceError').text(response.errors.discountPrice);
            }
          }
        }
      },
      error: function() {

        showErrorAlert();
      }
    });
  });

  $('#country').on('change', function() {
    var countryId = $(this).val();
    if (countryId) {
      $.ajax({
        url: '<?php echo $urlval ?>admin/ajax/product/get_cities.php',
        type: 'POST',
        data: {
          country_id: countryId
        },
        success: function(data) {
          $('#city').html(data);
        },
        error: function() {
          alert('Error fetching cities. Please try again.');
        }
      });
    } else {
      $('#city').html('<option value="" disabled>Select City</option>');
    }
  });

  $('#category').on('change', function() {
    var catId = $(this).val();

    if (catId) {
      $.ajax({
        url: '<?php echo $urlval ?>admin/ajax/product/get_subcat.php',
        type: 'POST',
        data: {
          catId: catId
        },
        success: function(data) {
          $('#subcategory').html(data);
        },
        error: function() {
          alert('Error fetching cities. Please try again.');
        }
      });
    } else {
      $('#subcategory').html('<option value="" disabled selected>Select a city</option>');
    }
  });

  document.getElementById('gallery').addEventListener('change', function(e) {
    const files = e.target.files;
    const previewContainer = document.getElementById('imagePreview');

    // Check the current number of images in the preview container
    if (previewContainer.children.length >= 8) {
        alert('You can only upload up to 8 images.');
        return; // Prevent further upload if 8 images are already present
    }

    for (let i = 0; i < files.length; i++) {
        if (previewContainer.children.length >= 8) break; // Stop if 8 images are already uploaded

        const file = files[i];
        const reader = new FileReader();

        reader.onload = function(event) {
            const imageUrl = event.target.result;

            // Create the image card
            const imageCard = document.createElement('div');
            imageCard.classList.add('image-card');
            imageCard.style.cssText = `
                position: relative;
                width: 120px;
                height: 120px;
                overflow: hidden;
                border: 1px solid #ccc;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
                cursor: grab;
            `;
            imageCard.setAttribute('draggable', 'true');

            // Create the image element
            const img = document.createElement('img');
            img.src = imageUrl;
            img.style.cssText = `
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 8px;
            `;

            // Create the delete button with a distinct style
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.classList.add('btn', 'btn-danger', 'btn-sm');
            deleteBtn.style.cssText = `
                position: absolute;
                top: 5px;
                right: 5px;
                background-color: #ff4d4d;  /* More distinct red */
                color: white;
                border: none;
                border-radius: 50%;
                font-size: 16px;   /* Larger button */
                width: 30px;
                height: 30px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            `;
            deleteBtn.innerHTML = '&times;';
            
          
            deleteBtn.addEventListener('click', function() {
                previewContainer.removeChild(imageCard);  
            });

      
            imageCard.appendChild(img);
            imageCard.appendChild(deleteBtn);

        
            previewContainer.appendChild(imageCard);
        };

      
        reader.readAsDataURL(file);
    }
});
function goBack() {
    window.history.back(); 
  }
</script>



</body>

</html>