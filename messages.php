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
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    width: 80%;
    max-width: 600px;
    border-radius: 5px;
    position: relative;
    box-sizing: border-box;
}


.modal-content h2 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}


.close-btn {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    top: 10px;
    right: 15px;
}

.close-btn:hover,
.close-btn:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

#transactionHistory {
    max-height: 400px;
    overflow-y: auto;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
}

@media (max-width: 768px) {

    .nav-tabs .nav-link {
        font-weight: 400;
        font-size: 9px;
    }

    .d-flex {
        flex-direction: column;
        align-items: stretch;
        /* gap: 5px; */
    }

    .btn-delete-upload {
        display: flex;
        margin-top: 10%;
        flex-direction: column;
        gap: 10px;
    }

    .btn-mobile {
        margin-bottom: 10px;
    }

}
</style>
<div class="container mt-4 pb-5">
    <ul class="nav nav-tabs justify-content-between" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button"
                role="tab">
                <i class="fas fa-upload me-2"></i><?= $lan['upload_new_product'] ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="view-products-tab" data-bs-toggle="tab" data-bs-target="#view-products"
                type="button" role="tab">
                <i class="fas fa-box me-2"></i><?= $lan['view_my_products']?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <!-- <button
            class="nav-link"
            id="messages-tab"
            data-bs-toggle="tab"
            data-bs-target="#messages"
            type="button"
            role="tab"
          >
            <i class="fas fa-comment me-2"></i>Messages
          </button> -->
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="favourite-tab" data-bs-toggle="tab" data-bs-target="#favourite" type="button"
                role="tab">
                <i class="fas fa-heart me-2"></i><?= $lan['favourites']?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button"
                role="tab">
                <i class="fas fa-user me-2"></i><?= $lan['my_details']?>
            </button>
        </li>
    </ul>
    <div class="tab-content mt-4" id="myTabContent">
        <div class="tab-content mt-4" id="myTabContent">
            <div class="tab-pane fade show active" id="upload" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title pb-3"><?= $lan['upload_new_product'] ?></h3>
                        <p class="card-subtitle text-muted">
                            <?= $lan['fill_detail_list'] ?>
                        </p>
                    </div>
                    <div class="card-body">
                        <form id="productForm" enctype="multipart/form-data">
                            <h5><?= $lan['basic_info'] ?></h5>

                            <!-- Product Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label"><?= $lan['product_title'] ?></label>
                                <input type="text" class="form-control" id="title" name="productName"
                                    placeholder="<?= $lan['enter'] . ' ' . $lan['product_title'] ?>" required />
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category" class="form-label"><?= $lan['category'] ?></label>
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

                            <!-- Sub Category -->
                            <div class="mb-3">
                                <label for="subcategory" class="form-label"><?= $lan['subcategory'] ?></label>
                                <select class="form-select" id="subcategory" name="subcategory" required></select>
                            </div>

                            <!-- Condition -->
                            <div class="mb-3">
                                <label class="form-label"><?= $lan['condition'] ?></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="conditionNew"
                                        value="new" checked required />
                                    <label class="form-check-label"
                                        for="conditionNew"><?= $lan['new_condition'] ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="conditionUsed"
                                        value="used" required />
                                    <label class="form-check-label"
                                        for="conditionUsed"><?= $lan['used_condition'] ?></label>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                <label for="price" class="form-label"><?= $lan['price'] ?></label>
                                <input type="number" class="form-control" name="price" id="price"
                                    placeholder="<?= $lan['enter'] . ' ' . $lan['price'] ?>" required />
                            </div>



                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label"><?= $lan['description'] ?></label>
                                <textarea class="form-control" id="description" rows="3" name="description"
                                    placeholder="<?= $lan['describe_your_product'] ?>" required></textarea>
                            </div>

                            <!-- Brand -->
                            <div class="mb-3">
                                <label for="brand" class="form-label"><?= $lan['brand'] ?></label>
                                <input type="text" class="form-control" id="brand" name="brand"
                                    placeholder="<?= $lan['enter'] . ' ' . $lan['brand'] ?>" required />
                            </div>

                            <!-- Country -->
                            <div class="mb-3">
                                <label for="country" class="form-label"><?= $lan['country'] ?></label>
                                <select id="country" name="country" class="form-select" required>
                                    <option value="" disabled selected><?= $lan['Select_country'] ?></option>
                                    <?php
                  $countries = $dbFunctions->getData('countries');
                  foreach ($countries as $cont) {
                    echo '<option value="' . $security->decrypt($cont['id']) . '">' . $security->decrypt($cont['name']) . '</option>';
                  }
                  ?>
                                </select>
                            </div>

                            <!-- City -->
                            <div class="mb-3">
                                <label for="city" class="form-label"><?= $lan['city'] ?> <span
                                        style="color: red;">*</span></label>
                                <select id="city" name="city" class="form-select" required>
                                    <option value="" disabled selected><?= $lan['Select_city'] ?></option>
                                </select>
                                <div class="text-danger" id="cityError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="aera" class="form-label">Aera <span style="color: red;">*</span></label>
                                <select id="aera" name="aera" class="form-select">
                                    <option value="" disabled selected><?= $lan['Select_city'] ?></option>
                                </select>
                                <div class="text-danger" id="aera_Error"></div>
                            </div>

                            <h5><?= $lan['media'] ?></h5>

                            <!-- Product Images -->
                            <!-- <div class="mb-3">
                <label for="images" class="form-label"> $lan['product_images']</label>
                <input class="form-control" type="file" id="images" name="image" multiple required />
                <div id="imagePreview" class="upload-preview"></div>
              </div> -->

                            <!-- Gallery Images -->
                            <div class="form-group mb-3"
                                style="padding: 20px; border: 2px solid #28a745; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">
                                <label for="gallery" class="custom-file-upload"><?= $lan['upload_gallery'] ?></label>
                                <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple required>
                                <div id="imagePreview" class="image-preview"></div>
                                <div class="text-danger" id="galleryError"></div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="btn btn-success float-end"><?= $lan['publish_listing'] ?></button>
                            <div id="formMessage" class="mt-3"></div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
        <div class="tab-pane fade" id="view-products" role="tabpanel">
            <h3 class="mb-4"><?= $lan['view_my_products']?></h3>
            <div class="row">
                <?php
        $productFun->getProductsForUser(base64_decode($_SESSION['userid']),$lan);
        ?>
            </div>
        </div>

        <div class="tab-pane fade" id="favourite" role="tabpanel">
            <h3 class="mb-4"><?= $lan['hi']?> <?php echo $_SESSION['username'] ?>, <?= $lan['you_have']?> <?php $isFavorit = $productFun->getUserFavorites(base64_decode($_SESSION['userid']));
                                                                        echo $isFavorit['count'];
                                                                        ?> <?= $lan['saved_ads']?></h3>
            <div class="row">
                <?php
        foreach ($isFavorit['favorites'] as $favorite) {
          $description = $favorite['description'];
          $words = explode(" ", $description);
          $description = count($words) > 5 ? implode(" ", array_slice($words, 0, 5)) . '...' : $description;
          echo '
              <div class="col-md-6 col-lg-4 mb-4">
                  <div class="favourite-item position-relative">
                      <img src="' . htmlspecialchars($favorite['image']) . '" alt="' . htmlspecialchars($favorite['name']) . '" class="img-fluid" />
                      <a data-productid="' . $favorite['id'] . '" id="favorite-button">
                      <i class="fas fa-heart heart-icon"></i>
                      </a>
                      <a href="'.$urlval.'detail.php?slug='.$favorite['slug'].'">
                      <div class="p-3">
                          <h5 class="mb-1">' . htmlspecialchars($favorite['name']) . '</h5>
                          <p class="mb-2">' . htmlspecialchars($description) . '</p>
                          <p class="mb-0">
                              <strong>£' . number_format($favorite['price'], 2) . '</strong>
                          </p>
                      </div>
                      </a>
                  </div>
              </div>';
        }
        ?>
            </div>
        </div>
        <div class="tab-pane fade" id="details" role="tabpanel">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title mb-4"><?= $lan['my_details']?></h3>
                            <form id="userDetailsForm" onsubmit="submitForm(event)">
                                <div id="responseMessage" class="alert" style="display:none;"></div>
                                <div class="mb-3">
                                    <label for="fullName" class="form-label"><?= $lan['full_name']?></label>
                                    <input type="text" class="form-control" id="fullName"
                                        value="<?php echo $_SESSION['username'] ?>" readonly />
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label"><?= $lan['email_address']?></label>
                                    <input type="email" class="form-control" id="email"
                                        value="<?php echo $_SESSION['email'] ?>" readonly />
                                </div>

                                <h4 class="mt-4 mb-3"><?= $lan['contact_detail']?></h4>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="country" class="form-label">Country<?= $lan['country']?></label>
                                        <input type="text" class="form-control" id="country"
                                            value="<?php echo $userData[0]['country'] ?? '' ?>" />
                                    </div>
                                    <div class="col">
                                        <label for="city" class="form-label"><?= $lan['city']?></label>
                                        <input type="text" class="form-control" id="city"
                                            value="<?php echo $userData[0]['city'] ?? '' ?>" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="contactNumber" class="form-label"><?= $lan['contact_number']?></label>
                                    <input type="tel" class="form-control" id="contactNumber"
                                        value="<?php echo $userData[0]['number'] ?? '' ?>" />
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label"><?= $lan['address']?></label>
                                    <textarea class="form-control" id="address"
                                        rows="3"><?php echo $userData[0]['address'] ?? '' ?></textarea>
                                </div>
                                <input type="hidden" name="token" id="csrf_token_update_info"
                                    value="<?php echo $CsrfProtection->generateToken() ?>">

                                <button type="submit"
                                    class="btn btn-primary btn-mobile"><?= $lan['save_contact_deatail']?></button>

                                <button type="button" class="btn btn-button btn-mobile"
                                    onclick="openPasswordModal()"><?= $lan['edit_password']?></button>
                                <button type="button" class="btn btn-button btn-mobile"
                                    onclick="openTransactionHistory()"><?= $lan['View_full_transaction_history']?></button>
                            </form>


                        </div>
                        <div id="passwordModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <span class="close" onclick="closePasswordModal()">&times;</span>
                                <h4>Update Password</h4>
                                <form id="passwordForm" onsubmit="updatePassword(event)">
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label"><?= $lan['new_password']?></label>
                                        <input type="password" class="form-control" id="newPassword" required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword"
                                            class="form-label"><?= $lan['confirm_password']?></label>
                                        <input type="password" class="form-control" id="confirmPassword" required />
                                    </div>
                                    <input type="hidden" name="token" id="csrf_token_password_chnage"
                                        value="<?php echo $CsrfProtection->generateToken() ?>">
                                    <button type="submit" class="btn btn-success"><?= $lan['save_password']?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="transactionHistoryModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2><?= $lan['transaction_history'] ?></h2>
            <div id="transactionHistory"></div>
            <!-- View More Button -->
            <a href="<?= $urlval?>transaction_history.php" class="btn btn-primary"
                style="display: block; margin-top: 20px; text-align: center;">
                <?= $lan['view_more'] ?>
            </a>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>
<script src="<?= $urlval ?>custom/js/messages.js"></script>
<script>
$(document).ready(function() {
    $('.btn-delete').on('click', function() {
        const productId = $(this).data('product-id');
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: '<?= $urlval ?>ajax/delete_product.php',
                method: 'POST',
                data: {
                    id: productId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message); 
                        location.reload();
                    } else {
                        alert(
                            'Product deleted successfully!'
                        ); 
                        location.reload();
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the product.');
                }
            });
        }
    });

});

const favoriteButton = document.getElementById('favorite-button');

favoriteButton.addEventListener('click', function() {
    const productId = this.getAttribute('data-productid');

    fetch('<?= $urlval ?>ajax/favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: productId
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                favoriteButton.innerHTML = data.isFavorited ?
                    '<i class="fas fa-heart heart-icon"></i>' :
                    '<i class="far fa-heart heart-icon" style="color=#000"></i>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
});



function submitForm(event) {
    event.preventDefault();

    const formData = new FormData();
    formData.append('country', document.getElementById('country').value);
    formData.append('city', document.getElementById('city').value);
    formData.append('contactNumber', document.getElementById('contactNumber').value);
    formData.append('address', document.getElementById('address').value);
    formData.append('token', document.getElementById('csrf_token_update_info').value);

    const responseMessageDiv = document.getElementById('responseMessage');
    responseMessageDiv.style.display = 'none';

    console.log('Submitting form...');

    fetch('<?= $urlval ?>ajax/update_user_details.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response received:', data);

            if (data.status === 'success') {
                responseMessageDiv.className = 'alert alert-success';
                responseMessageDiv.innerText = data.message || 'Contact details updated successfully.';
            } else {
                responseMessageDiv.className = 'alert alert-danger';
                responseMessageDiv.innerText = data.message || 'Error updating contact details.';
            }

            responseMessageDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            responseMessageDiv.className = 'alert alert-danger';
            responseMessageDiv.innerText = 'An error occurred while updating contact details.';
            responseMessageDiv.style.display = 'block';
        });
}

function openPasswordModal() {
    document.getElementById('passwordModal').style.display = 'block';
}

function closePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
}


function updatePassword(event) {
    event.preventDefault();

    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const token = document.getElementById('csrf_token_password_chnage').value;


    if (newPassword !== confirmPassword) {
        displayMessage('Passwords do not match!', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('password', newPassword);
    formData.append('token', token);

    fetch('<?= $urlval ?>ajax/update_password.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayMessage(data.message, 'success');

                setTimeout(() => {
                    closePasswordModal();
                    location.reload();
                }, 5000);
            } else {
                displayMessage(data.message, 'error');
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayMessage(message, type) {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = message;
    messageDiv.className = type === 'success' ? 'alert alert-success' : 'alert alert-danger';
    messageDiv.style.display = 'block';
}

$('#productForm').on('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: '<?= $urlval ?>ajax/addproductnew.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            let parsedResponse = JSON.parse(response); 
            if (parsedResponse.success) {
                displayMessage('Product added successfully!', 'success');
                $('#productForm')[0].reset(); 
            } else if (parsedResponse.errors) {
                displayMessage('There are errors in the form. Please fix them and try again.',
                    'danger');
                handleErrors(parsedResponse.errors);
            }
        },
        error: function() {
            displayMessage('An error occurred while processing your request. Please try again.',
                'danger');
        }
    });
});

function displayMessage(message, type) {
    const formMessage = $('#formMessage');
    formMessage.html(`<div class="alert alert-${type}" role="alert">${message}</div>`);
}

function showErrorAlert() {
    alert('There was an error posting your ad. Please try again.');
}

function handleErrors(errors) {

    for (let field in errors) {
        let errorMessage = errors[field];
        $('#' + field + 'Error').text(errorMessage);
    }
}



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
        $('#subcategory').html('<option value="" disabled selected>Select a Category</option>');
    }
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
$('#city').on('change', function() {
    var cityId = $(this).val();

    if (cityId) {
        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/product/get_areas.php',
            type: 'POST',
            data: {
                city_id: cityId
            },
            success: function(data) {
                $('#aera').html(data);
            },
            error: function() {
                alert('Error fetching areas. Please try again.');
            }
        });
    } else {
        $('#aera').html('<option value="" disabled selected>Select an area</option>');
    }
});

function openTransactionHistory() {
 
    document.getElementById('transactionHistoryModal').style.display = 'block';


    fetch('<?=$urlval?>ajax/get_transaction_history.php') 
        .then(response => response.json()) 
        .then(data => {
            let historyHtml = '<ul>';
            data.forEach(transaction => {
                historyHtml +=
                    `<li>Transaction ID: ${transaction.id} - Amount: $${transaction.amount} - Date: ${transaction.date}</li>`;
            });
            historyHtml += '</ul>';
            document.getElementById('transactionHistory').innerHTML = historyHtml;
        })
        .catch(error => {
            console.error('Error fetching transaction history:', error);
            document.getElementById('transactionHistory').innerHTML = 'Failed to load transaction history.';
        });
}


function closeModal() {
    document.getElementById('transactionHistoryModal').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function () {
    function activateTabFromHash() {
        const urlHash = window.location.hash; 
        if (urlHash) {
            const tabTrigger = document.querySelector(`[data-bs-target="${urlHash}"]`);
            if (tabTrigger) {
                const tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }
    }

    activateTabFromHash();
    const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    tabLinks.forEach(tabLink => {
        tabLink.addEventListener("shown.bs.tab", function (event) {
            const targetHash = event.target.getAttribute("data-bs-target"); 
            history.replaceState(null, null, targetHash); 
        });
    });

    window.addEventListener("hashchange", activateTabFromHash);
});


</script>
</body>

</html>