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
?>


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
      <div class="mb-3">
        <label for="title" class="form-label">Product Title</label>
        <input type="text" class="form-control" id="title" name="productName" placeholder="Enter product title" required />
      </div>
      <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <select class="form-select" id="category" name="category" required>
          <?php
          $findCate = $categoryManager->getAllCategoriesHeaderMenu();
          if ($findCate['status'] == 'success') {
              foreach ($findCate['data'] as $category) {
                  echo '<option value="' . $category['id'] . '">' . $category['category_name'] . '</option>';
              }
          }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="subcategory" class="form-label">Sub Category</label>
        <select class="form-select" id="subcategory" name="subcategory" required></select>
      </div>
      <div class="mb-3">
        <label class="form-label">Condition</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="condition" id="conditionNew" value="new" checked required />
          <label class="form-check-label" for="conditionNew">New</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="condition" id="conditionUsed" value="used" required />
          <label class="form-check-label" for="conditionUsed">Used</label>
        </div>
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" class="form-control" name="price" id="price" placeholder="Enter price" required />
      </div>

      <h5>Details</h5>
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" rows="3" name="description" placeholder="Describe your product" required></textarea>
      </div>
      <div class="mb-3">
        <label for="brand" class="form-label" name="brand">Brand</label>
        <input type="text" class="form-control" id="brand" name="brand" placeholder="Enter brand name" required />
      </div>
      <div class="mb-3">
        <label for="country" class="form-label">Country</label>
        <select id="country" name="country" class="form-select" required>
          <option value="" disabled selected>Select a country</option>
          <?php
          $countries = $dbFunctions->getData('countries');
          foreach ($countries as $cont) {
              echo '<option value="' . $security->decrypt($cont['id']) . '">' . $security->decrypt($cont['name']) . '</option>';
          }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="city" class="form-label">City <span style="color: red;">*</span></label>
        <select id="city" name="city" class="form-select" required>
          <option value="" disabled selected>Select a city</option>
        </select>
        <div class="text-danger" id="cityError"></div>
      </div>

      <h5>Media</h5>
      <div class="mb-3">
        <label for="images" class="form-label">Product Images</label>
        <input class="form-control" type="file" id="images" name="image" multiple required />
        <div id="imagePreview" class="upload-preview"></div>
      </div>
      <div class="form-group mb-3" style="padding: 20px;border: 2px solid #28a745;border-radius: 10px;box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);background-color: #f9f9f9;">
        <label for="gallery" class="custom-file-upload">Upload Gallery Images</label>
        <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple required>
        <div id="imagePreview" class="image-preview"></div>
        <div class="text-danger" id="galleryError"></div>
      </div>

      <button type="submit" class="btn btn-success float-end">Publish Listing</button>
    </form>
  </div>
</div>


<?php
    include_once 'footer.php';
    ?>
   
    </script>
    </body>
    </html>