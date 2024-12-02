<form id="productForm" enctype="multipart/form-data">
                <ul class="nav nav-pills mb-3 " id="pills-tab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button 
                      class="nav-link btn-button "
                      id="pills-basic-tab"
                      data-bs-toggle="pill"
                      data-bs-target="#pills-basic"
                      type="button"
                      role="tab"
                    >
                      Basic Info
                    </button>
                  </li>
                  <li class="nav-item pleft" role="presentation">
                    <button
                      class="nav-link btn-button"
                      id="pills-details-tab"
                      data-bs-toggle="pill"
                      data-bs-target="#pills-details"
                      type="button"
                      role="tab"
                    >
                      Details
                    </button>
                  </li>
                  <li class="nav-item pleft" role="presentation">
                    <button
                      class="nav-link btn-button"
                      id="pills-media-tab"
                      data-bs-toggle="pill"
                      data-bs-target="#pills-media"
                      type="button"
                      role="tab"
                    >
                      Media
                    </button>
                  </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                  <div
                    class="tab-pane fade show "
                    id="pills-basic"
                    role="tabpanel"
                  >
                    <div class="mb-3">
                      <label for="title" class="form-label"
                        >Product Title</label
                      >
                      <input
                        type="text"
                        class="form-control"
                        id="title"
                        name="productName"
                        placeholder="Enter product title"
                      />
                    </div>
                    <div class="mb-3">
                      <label for="category" class="form-label">Category</label>
                      <select class="form-select" id="category" name="category">
                      <?php
                      $findCate = $categoryManager->getAllCategoriesHeaderMenu();
                      if ($findCate['status'] == 'success') {
                          foreach ($findCate['data'] as $category) {
                            echo '
                          <option value="' . $category['id'] . '">' . $category['category_name'] . '</option>
                        ';
                          }
                        }
                      
                      ?>
                       
                      </select>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label" >Sub Category</label>
                        <select class="form-select" id="subcategory" name="subcategory">
                          
                        </select>
                      </div>
                    <div class="mb-3">
                      <label class="form-label">Condition</label>
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="radio"
                          name="condition"
                          id="conditionNew"
                          value="new"
                          checked
                        />
                        <label class="form-check-label" for="conditionNew"
                          >New</label
                        >
                      </div>
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="radio"
                          name="condition"
                          id="conditionUsed"
                          value="used"
                          
                        />
                        <label class="form-check-label" for="conditionUsed"
                          >Used</label
                        >
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="price" class="form-label">Price</label>
                      <input
                        type="number"
                        class="form-control"
                        name="price"
                        id="price"
                        placeholder="Enter price"
                      />
                    </div>
                  </div>
                  <div class="tab-pane fade" id="pills-details" role="tabpanel">
                    <div class="mb-3">
                      <label for="description" class="form-label"
                        >Description</label
                      >
                      <textarea
                        class="form-control"
                        id="description"
                        rows="3"
                        name="description"
                        placeholder="Describe your product"
                      ></textarea>
                    </div>
                    <div class="mb-3">
                      <label for="brand" class="form-label" name="brand">Brand</label>
                      <input
                        type="text"
                        class="form-control"
                        id="brand"
                        placeholder="Enter brand name"
                      />
                    </div>
                    <div class="mb-3">
                    <label for="country" class="form-label">Country </label>
                        <select id="country" name="country" class="form-select" >
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
                                    <select id="city" name="city" class="form-select" >
                                        <option value="" disabled selected>Select a city</option>
                                    </select>
                                    <div class="text-danger" id="cityError"></div> <!-- Error message -->
                      </div>
                  </div>
                  <div class="tab-pane fade" id="pills-media" role="tabpanel">
                    <div class="mb-3">
                      <label for="images" class="form-label"
                        >Product Images</label
                      >
                      <input
                        class="form-control"
                        type="file"
                        id="images"
                        name="image"
                        multiple
                      />
                      <div id="imagePreview" class="upload-preview"></div>
                    </div>
                    <div class="form-group mb-3" style="padding: 20px;border: 2px solid #28a745;border-radius: 10px;box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);background-color: #f9f9f9;">
                    <label for="gallery" class="custom-file-upload">Upload Gallery Images</label>
                    <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                    <div id="imagePreview" class="image-preview"></div>
                    <div class="text-danger" id="galleryError"></div> 
                </div>
                  </div>
                </div>
              </form>



<?php
require_once 'global.php'; 


// Sample data to insert
$data = [
    'username' => 'Hackruf_01',
    'email' => 'user6@example.com',
    'password' => 'Click@123' // Consider hashing passwords before saving
];

// Call setData to insert a new user
if ($dbFunctions->setData('users', $data)) {
    echo "User data inserted successfully.";
} else {
    echo "Failed to insert user data.";
}

?>

