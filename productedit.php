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
var_Dump($product);
?>
<style>
  .image-card {
    position: relative;
    width: 100px;
    height: 100px;
    overflow: hidden;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.image-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-card .delete-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: #ff4d4d;
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 12px;
    line-height: 1;
    cursor: pointer;
    padding: 5px;
}

</style>

<div class="card col-6 mt-5 m-auto mb-5">
  <div class="card-header">
    <h3 class="card-title pb-3">Upload New Product</h3>
    <p class="card-subtitle text-muted">
      Fill in the details to list your product for sale.
    </p>
  </div>
  <div class="card-body">
    <form id="productForm" enctype="multipart/form-data">
      <h5>Basic Info</h5>
      <!-- Product Title -->
      <div class="mb-3">
        <label for="title" class="form-label">Product Title</label>
        <input type="text" class="form-control" id="title" name="productName" 
               value="<?php echo htmlspecialchars($product['product']['product_name']); ?>" 
               placeholder="Enter product title" required />
        <input type="hidden" class="form-control" id="title" name="productId" value="<?php echo $product['product']['product_id']; ?>" />
        <div class="text-danger" id="productNameError"></div> 
      </div>

      <!-- Category -->
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
        <div class="text-danger" id="categoryError"></div> <!-- Error message -->
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
    <div class="text-danger" id="subcategoryError"></div> <!-- Error message -->
</div>

      <!-- Condition -->
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
        <div class="text-danger" id="conditionError"></div> <!-- Error message -->
      </div>

      <!-- Price -->
      <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" class="form-control" name="price" id="price" 
               value="<?php echo htmlspecialchars($product['product']['price']); ?>" 
               placeholder="Enter price" required />
        <div class="text-danger" id="priceError"></div> <!-- Error message -->
      </div>

      <h5>Details</h5>
      <!-- Description -->
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" rows="3" name="description" 
                  placeholder="Describe your product" required><?php echo htmlspecialchars($product['product']['product_description']); ?></textarea>
        <div class="text-danger" id="descriptionError"></div> <!-- Error message -->
      </div>

      <!-- Brand -->
      <div class="mb-3">
        <label for="brand" class="form-label">Brand</label>
        <input type="text" class="form-control" id="brand" name="brand" 
               value="<?php echo htmlspecialchars($product['product']['brand']); ?>" 
               placeholder="Enter brand name" required />
        <div class="text-danger" id="brandError"></div> <!-- Error message -->
      </div>

      <!-- Country -->
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
        <div class="text-danger" id="countryError"></div> <!-- Error message -->
      </div>

<?php
      $countryId = $product['product']['country_id']; // Selected country
$selectedCityId = $product['product']['city_id']; // Current city
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
                . htmlspecialchars($city['name']) .'</option>';
        }
        ?>
    </select>
    <div class="text-danger" id="cityError"></div> <!-- Error message -->
</div>
   

      <h5>Media</h5>
      <?php

if (!empty($product['gallery_images'])) {
    usort($product['gallery_images'], function ($a, $b) {
        return $a['sort'] <=> $b['sort'];
    });
}
?>

<div class="mb-3">
    <label for="gallery" class="form-label">Product Images</label>
    <input class="form-control" type="file" id="gallery" name="gallery[]" multiple />
    <div id="imagePreview" class="upload-preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
        <?php
   
        if (!empty($product['gallery_images'])) {
            foreach ($product['gallery_images'] as $key => $image) {
                echo '
                <div class="image-card" style="position: relative; width: 100px; height: 100px; overflow: hidden; border: 1px solid #ccc; border-radius: 5px;">
                    <img src="' . htmlspecialchars($image['image_path']) . '" alt="Product Image" style="width: 100%; height: 100%; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-key="' . $image['image_id'] . '" style="position: absolute; top: 5px; right: 5px; font-size: 12px;">&times;</button>
                </div>';
            }
        }
        ?>
    </div>
</div>
      <button type="submit" class="btn btn-success float-end">Update Listing</button>
    </form>
  </div>
</div>




<?php
    include_once 'footer.php';
    ?>
   
   <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const imagePreview = document.getElementById('imagePreview');


    imagePreview.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-btn')) {
            const imageCard = e.target.closest('.image-card');
            const key = e.target.getAttribute('data-key');

            fetch('delete_image.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ image_key: key })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Image deleted successfully');
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
        onEnd: function (evt) {
    
            const updatedOrder = [];
            document.querySelectorAll('.image-card').forEach((card, index) => {
                const key = card.querySelector('.delete-btn').getAttribute('data-key'); 
                updatedOrder.push({ key: key, sort: index });  
            });

            console.log('Updated Order:', updatedOrder);

           
            fetch('<?= $urlval?>ajax/update_sort_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ order: updatedOrder })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Sort order updated successfully');
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

</script>



    </body>
    </html>