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
    .msg-head-innder {
    TOP: 4PX !important;
    POSITION: ABSOLUTE !important;
    left: -30PX  !important;
   
}
/* .product-msg-tp{
    display: block !important;
} */

}

.product-image-container {
    position: relative;
}

.product-image-container img {
    width: 100%;
    height: auto;
}

.label {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 5px 10px;
    border-radius: 3px;
    color: white;
    font-size: 14px;
}

.label-danger {
    background-color: red;
}

.label-success {
    background-color: green;
}
.message-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.message-item {
    flex: 1;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.delete-icon {
    margin-left: 10px;
    color: red;
    cursor: pointer;
    display: none;
    font-size: 18px;
}

.message-container:hover .delete-icon {
    display: block;
}

</style>

<div class="container mt-4 pb-5">
    <ul class="nav nav-tabs justify-content-between" id="myTab" role="tablist">
        <!-- <li class="nav-item" role="presentation"> -->
            <!-- <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button"
                role="tab">
                <i class="fas fa-upload me-2"></i><?php //echo $lan['upload_new_product'] ?>
            </button> -->
        <!-- </li> -->
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="view-products-tab" data-bs-toggle="tab" data-bs-target="#view-products"
                type="button" role="tab">
                <i class="fas fa-box me-2"></i><?= $lan['view_my_products']?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button
            class="nav-link"
            id="messages-tab"
            data-bs-toggle="tab"
            data-bs-target="#messages543"
            type="button"
            role="tab"
          >
            <i class="fas fa-comment me-2"></i>Messages
          </button>
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
    <h3 class="mb-4" style="font-size: 1.5rem; color: #333;">
        <?= $lan['hi']?> <?php echo $_SESSION['username'] ?>, <?= $lan['you_have']?> 
        <?php 
            $isFavorit = $productFun->getUserFavorites(base64_decode($_SESSION['userid']));
            echo $isFavorit['count']; 
        ?> <?= $lan['saved_ads']?>
    </h3>
    <div class="row">
        <?php
        foreach ($isFavorit['favorites'] as $favorite) {
            $description = $favorite['description'];
            $words = explode(" ", $description);
            $description = count($words) > 5 ? implode(" ", array_slice($words, 0, 5)) . '...' : $description;
            echo '
            <div class="col-md-6 col-lg-4 mb-4" style="padding: 0 15px;">
                <div class="favourite-item position-relative" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <img src="' . htmlspecialchars($favorite['image']) . '" alt="' . htmlspecialchars($favorite['name']) . '" class="" style="width: 100% !important; height: 200px !important; object-fit: cover;" />
                    <a data-productid="' . $favorite['id'] . '" id="favorite-button" style="position: absolute; top: 10px; right: 10px; color: #f00;">
                        <i class="fas fa-heart heart-icon" style="font-size: 1.5rem;"></i>
                    </a>
                    <a href="'.$urlval.'detail.php?slug='.$favorite['slug'].'" style="text-decoration: none; color: inherit;">
                        <div class="p-3" style="background-color: #fff;">
                            <h5 class="mb-1" style="font-size: 1.25rem; font-weight: bold;">' . htmlspecialchars($favorite['name']) . '</h5>
                            <p class="mb-2" style="font-size: 1rem; color: #555;">' . htmlspecialchars($description) . '</p>
                            <p class="mb-0" style="font-size: 1.25rem; font-weight: bold; color: #00494f;">
                                £' . number_format($favorite['price'], 2) . '
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
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first-name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first-name" placeholder="Enter first name" 
                                            value="<?php echo $userData[0]['first_name'] ?? '' ?>"
                                            <?php echo !empty($userData[0]['first_name']) ? 'readonly' : ''; ?>>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="last-name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last-name" placeholder="Enter last name" 
                                            value="<?php echo $userData[0]['last_name'] ?? '' ?>"
                                            <?php echo !empty($userData[0]['last_name']) ? 'readonly' : ''; ?>>
                                    </div>
                                </div>
                                                         <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username"
                                        value="<?php echo $_SESSION['username'] ?>" />
                                </div> 
                                <div class="mb-3">
                                    <label for="email" class="form-label"><?= $lan['email_address']?></label>
                                    <input type="email" class="form-control" id="email"
                                        value="<?php echo $_SESSION['email'] ?>" readonly />
                                </div>

                                <h4 class="mt-4 mb-3"><?= $lan['contact_detail']?></h4>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="country" class="form-label"><?= $lan['country']?></label>
                                        <input type="text" class="form-control" id="countryyy"
                                            value="<?php echo $userData[0]['country'] ?? '' ?>" />
                                    </div>
                                    <div class="col">
                                        <label for="city" class="form-label"><?= $lan['city']?></label>
                                        <input type="text" class="form-control" id="cityy"
                                            value="<?php echo $userData[0]['city'] ?? '' ?>" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="contactNumber" class="form-label"><?= $lan['contact_number']?></label>
                                    <input type="tel" class="form-control" id="contactNumber"
                                        value="<?php echo $userData[0]['number'] ?? '' ?>" />
                                </div>
                                <div class="mb-3">
                                    <label for="language" class="form-label">Language</label>
                                    <input type="text" class="form-control" id="language"
                                        value="<?php echo $userData[0]['language'] ?? '' ?>" />
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
                    
                    </div>
                </div>
            </div>
        </div>
        </div>
     

            <?php
include_once 'messages-inner.php';
?>
   </div>     

<!-- Transaction History Modal -->
<div id="transactionHistoryModal" class="modal" style="z-index:999;display: none; background-color: rgba(0, 0, 0, 0.5); padding: 50px;">
    <div class="modal-content" style="position: fixed;top: 35%;left: 33%;background-color: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto;">
        <span class="close-btn" onclick="closeModal()" style="font-size: 28px; color: #00494F; cursor: pointer; position: absolute; top: 15px; right: 20px;">&times;</span>
        <h2 style="color: #00494F; text-align: center; font-size: 24px; margin-bottom: 20px;"><?= $lan['transaction_history'] ?></h2>
        <div id="transactionHistory"></div>
        <!-- View More Button -->
        <a href="<?= $urlval?>transaction_history.php" class="btn" style="display: block; margin-top: 20px; text-align: center; background-color: #00494F; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; font-size: 16px;">
            <?= $lan['view_more'] ?>
        </a>
    </div>
</div>

<!-- Password Update Modal -->
<div id="passwordModal" class="modal" style="z-index:999; display: none; background-color: rgba(0, 0, 0, 0.5); padding: 50px;">
    <div class="modal-content" style="position: fixed;top: 35%;left: 33%;background-color: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto;">
        <span class="close" onclick="closePasswordModal()" style="font-size: 28px; color: white; cursor: pointer; position: absolute; top: 15px; right: 20px;">&times;</span>
        <h4 style="color: white; text-align: center; font-size: 24px; margin-bottom: 20px;">Update Password</h4>
        <form id="passwordForm" onsubmit="updatePassword(event)">
            <div class="mb-3">
                <label for="newPassword" class="form-label" style="color: #00494F; font-size: 16px;"><?= $lan['new_password']?></label>
                <input type="password" class="form-control" id="newPassword" required style="border-color: #00494F; padding: 10px 15px; font-size: 16px;"/>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label" style="color: #00494F; font-size: 16px;"><?= $lan['confirm_password']?></label>
                <input type="password" class="form-control" id="confirmPassword" required style="border-color: #00494F; padding: 10px 15px; font-size: 16px;"/>
            </div>
            <input type="hidden" name="token" id="csrf_token_password_chnage" value="<?php echo $CsrfProtection->generateToken() ?>">
            <button type="submit" class="btn" style="background-color: #00494F; color: white; padding: 12px 24px; border-radius: 5px; border: none; cursor: pointer; font-size: 16px;">
                <?= $lan['save_password']?>
            </button>
        </form>
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



function submitForm(event) {
    event.preventDefault();

    const formData = new FormData();
        formData.append('country', document.getElementById('countryyy').value);
        formData.append('first-name', document.getElementById('first-name').value);
        formData.append('last-name', document.getElementById('last-name').value);
        formData.append('city', document.getElementById('cityy').value);
        formData.append('contactNumber', document.getElementById('contactNumber').value);
        formData.append('address', document.getElementById('address').value);
        formData.append('language', document.getElementById('language').value);
        formData.append('username', document.getElementById('username').value);
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
            console.log('Response received:', data.status);

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



$(document).ready(function() {
    loadChatList();


    $(document).on('click', '.chat-list-item', function() {
        var conversationId = $(this).data('conversation-id');
        loadMessages(conversationId);
    });


    $('#send-message-form').submit(function(event) {
        event.preventDefault();
        var message = $('#message-input').val();
        var conversationId = $('#chat-box').data('conversation-id');

        if (message !== '') {
            sendMessage(conversationId, message);
        }
    });
});


function loadChatList() {
    $.ajax({
        url: '<?= $urlval?>ajax/fetch_conversations.php',
        method: 'GET',
        success: function(response) {
            $('#chat-list').html(response);
        }
    });
}


function loadMessages(conversationId, productName, productImage, statusMessage) {
    $.ajax({
        url: '<?= $urlval ?>ajax/fetch_messages.php',
        method: 'POST',
        data: {
            conversation_id: conversationId
        },
        success: function(response) {
            $('#message-body').html(response);
            if ($(window).width() <= 768) {
                $(".chatbox").addClass('showbox');
                $(".hide-by").show();
                $(".back-button").show();
            } else {
                $(".hide-by").hide();
            }

          
            if (statusMessage.toLowerCase() === 'expired') {
                $('.send-box').hide();  
            } else {
                $('.send-box').show();  
                console.log('not');
            }

            $('#chat-box').data('conversation-id', conversationId);
            $('#message-body').scrollTop($('#message-body')[0].scrollHeight);

            const firstLetter = productName.charAt(0).toUpperCase();
            const profileLink = `user_profile.php?username=${productName}`;

            var headerHTML = `
                <div class="col-8 d-flex align-items-center">
                    <div class="rounded-circle text-white bg-secondary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;">
                        ${firstLetter}
                    </div>
                    <a href="${profileLink}" class="ms-2" style="font-size: 16px; font-weight: bold; color: white; text-decoration: none;">
                        ${productName}
                    </a>
                </div>
            `;

            $('.msg-head-innder').html(headerHTML); 
        }
    });
}




document.getElementById('file-upload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.innerHTML = `<img src="${e.target.result}" style="max-width: 100px; max-height: 100px; border-radius: 10px;">`;
            document.getElementById('image-file').value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('send-message-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const message = document.getElementById('message-input').value;
    const imageFile = document.getElementById('file-upload').files[0];
    const conversationId = $('#chat-box').data('conversation-id');
    const formData = new FormData();
    formData.append('message', message);
    formData.append('conversation_id', conversationId);

    if (imageFile) {
        formData.append('attachments[]', imageFile);
    }

    $.ajax({
        url: '<?= $urlval ?>ajax/send_message.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#message-input').val('');
            $('#file-upload').val('');
            $('#image-preview').html('');
            loadMessages(conversationId);
        }
    });
});




$(".chat-icon").click(function() {
    $(".chatbox").removeClass('showbox');
});

function hidepopup() {
    $('#popup-overlay').hide();
    $('#product-info-popup').hide();

    $('body').css('overflow', 'auto');
}
$(window).on('scroll', function() {
    var scrollThreshold = 600;

    if ($(window).scrollTop() > scrollThreshold) {
        hidepopup();
    }
});

$(document).on('click', '.back-button', function() {
    $(".chatbox").removeClass('showbox');
    $(".back-button").hide();
});


$(document).ready(function () {
    const emojis = ['😊', '😂', '😍', '😢', '😎', '👍', '🎉', '❤️', '🔥', '💯', '😜', '🥳', '😏', '🙌', '💃', '🕺', '🤩', '😎', '🤗', '😇'];

    const emojiList = $('#emoji-list');

    emojis.forEach(emoji => {
        const span = $('<span>').text(emoji).css({
            fontSize: '25px',
            cursor: 'pointer',
            margin: '5px',
        }).click(function () {
            $('#message-input').val($('#message-input').val() + emoji);
            emojiList.hide();  
        });
        emojiList.append(span);
    });

    $('.emoji-picker i').click(function () {
        emojiList.toggle();
    });




});

function openImagePopup(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').style.display = 'flex';
    }

    function closeImagePopup() {
        document.getElementById('imageModal').style.display = 'none';
    }


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
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

document.addEventListener('DOMContentLoaded', function () {
    const chatId = getQueryParam('chatid');

    if (chatId) {
 
        const productName = "Demo Product"; 
        const statusMessage = "active"; 
        loadMessages(chatId, productName, statusMessage);
    }
});

function updateUrlWithChatId(chatId) {
    const newUrl = window.location.href.split('?')[0] + '?chatid=' + chatId;
    window.history.pushState({ path: newUrl }, '', newUrl);  // Update the URL without reloading the page
}

</script>
</body>

</html>