<?php
require_once("../../global.php");
include_once('../header.php');

$categories = $dbFunctions->getDatanotenc('categories', 'is_enable = 1');
//$subcategories = $dbFunctions->getDatanotenc('subcategories', 'is_enable = 1');
$countries = $dbFunctions->getDatanotenc('countries');
//$cities = $dbFunctions->getDatanotenc('cities');
include_once('style.php');
?>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">

                </div>
                <div class="col-md-12">
                    <h3 class="title-5 m-b-35">Products</h3>


                    <form method="GET" action="" class="mb-4 custom-form">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="product_name" class="form-control"
                                    placeholder="Search by Product Name" value="">
                            </div>

                            <div class="col-md-2">
                                <input type="number" name="min_price" class="form-control" placeholder="Min Price"
                                    value="">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="max_price" class="form-control" placeholder="Max Price"
                                    value="">
                            </div>

                            <div class="col-md-2">
                                <select id="category" name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php
                                    if(isset($categories)){
                                    foreach ($categories as $category) {
                                        echo '
                                    <option value="'.$category['id'].'">'.$category['category_name'].'</option>
                                        
                                        ';
                                    }
                                    }
                                    ?>

                                </select>
                            </div>

                            <div class="col-md-2">
                                <select id="subcategory" name="subcategory" class="form-select">
                                    <option value="">All Subcategory</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="product_type" class="form-select">
                                    <option value="">All Product Types</option>
                                    <option value="standard">Standard</option>
                                    <option value="premium">Premium</option>
                                    <option value="gold">Gold</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select id="country" name="country" class="form-select">
                                    <option value="">All Countries</option>
                                    <?php
                                    if(isset($countries)){
                                        foreach ($countries as $country) {
                                            echo '
                                    <option value="'.$country['id'].'">'.$country['name'].'</option>
                                        
                                        ';
                                        }
                                    }
                                    ?>

                                </select>
                            </div>

                            <div class="col-md-2">
                                <select id="city" name="city" class="form-select">
                                    <option value="">All Cities</option>
                                </select>
                            </div>


                            <div class="col-md-2">
                                <button type="submit" class="btn btn-warning custom-button">Filter</button>
                                <a href="<?= $urlval?>admin/product/add.php"
                                    class="btn btn-success custom-button">Add</a>
                            </div>
                        </div>
                    </form>





                    <div class="row" id="product-container">
                        <?php

                        ?>
                    </div>


                    <nav aria-label="Product Pagination">
                        <ul class="pagination"></ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
</div>
</div>






<?php
include_once('view.php');
include_once('../footer.php');
?>


<script>
$(document).ready(function() {
    fetchProducts(1);

    $('form').on('submit', function(e) {
        e.preventDefault();
        fetchProducts(1);
    });
});

function fetchProducts(page) {
    var product_name = $('input[name="product_name"]').val();
    var min_price = $('input[name="min_price"]').val();
    var max_price = $('input[name="max_price"]').val();
    var category = $('select[name="category"]').val();
    var subcategory = $('select[name="subcategory"]').val();
    var product_type = $('select[name="product_type"]').val();
    var country = $('select[name="country"]').val();
    var city = $('select[name="city"]').val();

    $.ajax({
        url: '<?php echo $urlval ?>admin/ajax/product/fetchpro.php',
        type: 'GET',
        data: {
            page: page,
            limit: 6,
            product_name: product_name,
            min_price: min_price,
            max_price: max_price,
            category: category,
            subcategory: subcategory,
            product_type: product_type,
            country: country,
            city: city
        },
        dataType: 'json',
        success: function(data) {
            $('#product-container').empty();
            if (data.products.length > 0) {
                $.each(data.products, function(index, product) {
                    console.log(product);
                    var productHTML = `
                    <div class="col-md-4">
                        <div class="card product-card mb-4 shadow-sm">
                            <img class="card-img-top" src="${product.image}" alt="Product image">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-text">
                                    <span class="discount-price">$${product.discount_price}</span>
                                    <span class="original-price text-muted"><del>$${product.price}</del></span>
                                </p>
                                <p class="card-text">${product.description}</p>
                                <div class="additional-info">
                                    <p class="card-text"><strong>Category:</strong> ${product.category}</p>
                                    <p class="card-text"><strong>Sub-Category:</strong> ${product.subcategory}</p>
                                    <p class="card-text"><strong>Product Type:</strong> ${product.product_type}</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="city-country">
                                        ${product.country} | ${product.city}
                                    </div>
                                    <div class="btn-group">
                                        <div style="position: relative; display: inline-block; width: 100%; margin-right: 5px; margin-top: 3px;">
                                            <select class="js-select2 user-status-select" data-id="${product.id}"
                                                style="
                                                width: 100%;
                                                background-color: #f5f5f5;
                                                color: #333;
                                                border: 1px solid #ddd;
                                                border-radius: 8px;
                                                padding: 5px 23px;
                                                font-size: 14px;
                                                appearance: none;
                                                transition: all 0.3s ease;
                                                cursor: pointer;
                                                " 
                                                onmouseover="this.style.backgroundColor=\'#fff\'; this.style.borderColor=\'#007bff\'; this.style.boxShadow=\'0px 4px 6px rgba(0, 0, 0, 0.1)\';" 
                                                onmouseout="this.style.backgroundColor=\'#f5f5f5\'; this.style.borderColor=\'#ddd\'; this.style.boxShadow=\'none\';"
                                                onfocus="this.style.borderColor=\'#007bff\'; this.style.boxShadow=\'0px 4px 6px rgba(0, 123, 255, 0.3)\';"
                                                onblur="this.style.borderColor=\'#ddd\'; this.style.boxShadow=\'none\';"
                                            >
                                                <option value="1"' ${product.is_enable == 1 ? 'selected' :''}>Activate</option>
                                                <option value="0"' ${product.is_enable == 0 ? 'selected' :''}>Block</option>
                                            </select>
                                            <span style="
                                                position: absolute;
                                                top: 50%;
                                                right: 10px;
                                                transform: translateY(-50%);
                                                pointer-events: none;
                                                color: #007bff;
                                                font-size: 12px;
                                            ">
                                                ▼
                                            </span>
                                        </div>

                                        <a href="#" class="btn btn-sm btn-warning view-product" data-id="${product.id}">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        
                                        <a href="#" class="btn btn-sm btn-danger delete-product" data-id="${product.id}">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    $('#product-container').append(productHTML);
                });

                setupPagination(data.total, page);
            } else {
                $('#product-container').html('<p>No products found.</p>');
            }

            // Add event listeners for view and delete product buttons
            $('.view-product').click(function(e) {
                e.preventDefault();
                var productId = $(this).data('id');
                showProductDetails(productId);
            });

            $('.delete-product').click(function(e) {
                e.preventDefault();
                var productId = $(this).data('id');
                deleteProduct(productId);
            });
        },
        error: function() {
            $('#product-container').html('<p>Error loading products.</p>');
        }
    });
}
$(document).on('change', '.user-status-select', function() {
    var userId = $(this).data('id');
    var status = $(this).val();

    $.ajax({
        url: '<?php echo $urlval ?>admin/ajax/product/update_status.php',
        type: 'POST',
        data: {
            id: userId,
            status: status
        },
        success: function(response) {
            alert('User status updated successfully!');
        },
        error: function(xhr, status, error) {
            // Handle error
            alert('An error occurred while updating status.');
        }
    });
});

function showProductDetails(productId) {
    $.ajax({
        url: '<?php echo $urlval ?>admin/ajax/product/fetchProductDetails.php',
        type: 'GET',
        data: {
            id: productId
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                var galleryImagesArray = data.product.gallery_images ? data.product.gallery_images.split(
                    ',') : [];

                var detailsHTML = `
                <div class="profileimagee"><img src="<?= $urlval?>${data.product.product_image}" alt="${data.product.product_name}" class="img-fluid mb-3 profileimg"></div>
                <h3>Gallery images</h3>    
                <div class="product-images multiple-items">
                    
                        
                        ${galleryImagesArray.map(image => `
                            <div><img src="<?= $urlval?>${image}" alt="Additional Image" class=""></div>
                        `).join('')}
                    </div>
                    <h4>${data.product.product_name}</h4>
                    <p><strong>Price:</strong> <span class="discount-price">$${data.product.product_discount_price}</span>
                    <span class="original-price text-muted"><del>$${data.product.product_price}</del></span></p>
                    <p>${data.product.product_description}</p>
                    <p><strong>Category:</strong> ${data.product.category_name}</p>
                    <p><strong>Sub-Category:</strong> ${data.product.subcategory_name}</p>
                    <p><strong>Product Type:</strong> ${data.product.product_type}</p>
                    <p><strong>Available in:</strong> ${data.product.country_name} | ${data.product.city_name}</p>
                `;
                $('#productDetailContent').html(detailsHTML);

                $('.multiple-items').slick({
                    infinite: true,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    arrows: true,
                    dots: true
                });

                $('#productDetailModal').modal('show');
            } else {
                alert('Product details not found.');
            }
        },
        error: function() {
            alert('Error loading product details.');
        }
    });
}




function deleteProduct(productId) {
    $.ajax({
        url: '<?php echo $urlval ?>admin/ajax/product/deleteProduct.php',
        type: 'POST',
        data: {
            id: productId
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                alert('Product deleted successfully.');
                fetchProducts(1);
            } else {
                alert('Error deleting product: ' + data.error);
            }
        },
        error: function() {
            alert('Error deleting product.');
        }
    });
}

function setupPagination(totalProducts, currentPage) {
    var totalPages = Math.ceil(totalProducts / 6);
    var paginationHTML = '';

    if (currentPage > 1) {
        paginationHTML +=
            `<li class="page-item"><a class="page-link" href="#" onclick="fetchProducts(${currentPage - 1})">Previous</a></li>`;
    } else {
        paginationHTML += `<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>`;
    }

    for (var i = 1; i <= totalPages; i++) {
        if (i == currentPage) {
            paginationHTML += `<li class="page-item active"><a class="page-link" href="#">${i}</a></li>`;
        } else {
            paginationHTML +=
                `<li class="page-item"><a class="page-link" href="#" onclick="fetchProducts(${i})">${i}</a></li>`;
        }
    }

    if (currentPage < totalPages) {
        paginationHTML +=
            `<li class="page-item"><a class="page-link" href="#" onclick="fetchProducts(${currentPage + 1})">Next</a></li>`;
    } else {
        paginationHTML += `<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>`;
    }

    $('.pagination').html(paginationHTML);
}
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
        $('#city').html('<option value="" disabled selected>Select a city</option>');
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