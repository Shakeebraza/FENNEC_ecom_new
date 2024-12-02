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

if (isset($_GET['productid'])) {
    $product_id = base64_decode($_GET['productid']);
    $getProductData = $dbFunctions->getDatanotenc('products', "id='$product_id'");
    $productImg = $urlval . $getProductData[0]['image'];
} else {
    echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>';
    exit();
}
?>
<style>
    /* Section width set to 90% */
    .drag-drop-video-area {
        width: 90%;
        margin: 0 auto;
        border: 2px dashed #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Drop-zone styling */
    .drop-zone {
        border: 2px dashed #28a745;
        padding: 30px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .drop-zone:hover {
        background-color: #e9f7ed;
    }

    .drop-zone.drag-over {
        background-color: #d4f5dc;
        border-color: #218838;
    }

    /* Product section styling */
    .product-section {
        background-color: #f0f8ff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    .product-section img {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* File items inside list */
    .file-item {
        font-size: 14px;
        border-left: 5px solid #28a745;
    }

    .file-item p {
        font-size: 12px;
        color: #555;
    }

    .file-item video {
        width: 100%;
        border-radius: 8px;
    }

    /* Button styling */
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #218838;
    }
    #product-video-section {
        background-color: #f9f9f9;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
    }

    #product-video-section h5 {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    /* Styling for the row holding video thumbnails */
    #product-video-list {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }

    /* Individual video item styling */
    .video-item {
        position: relative;
        width: 250px;
        height: 150px;
        border-radius: 8px;
        overflow: hidden;
        background-color: #e0e0e0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .video-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .video-thumbnail {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    /* Optional: Adding a play button overlay for videos */
    .video-item .play-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 32px;
        color: white;
        opacity: 0.7;
        cursor: pointer;
        transition: opacity 0.3s ease;
    }

    .video-item:hover .play-btn {
        opacity: 1;
    }

    /* Responsiveness: Adjust layout for smaller screens */
    @media (max-width: 768px) {
        #product-video-list {
            flex-direction: column;
            align-items: center;
        }

        .video-item {
            width: 90%;
            height: auto;
            margin-bottom: 15px;
        }
        #product-video-section{
            padding: 0px;
        }
        .col-4{
            width: 100%;
        }
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <div class="product-section mb-5">
                <div class="row">
                    <div class="col-md-4">
                        <img src="<?= htmlspecialchars($productImg) ?>" alt="Product Image" class="img-fluid rounded">
                    </div>
                    <div class="col-md-8">
                        <h3 class="text-primary"><?= htmlspecialchars($getProductData[0]['name']) ?></h3>
                        <p class="text-muted"><?= htmlspecialchars($getProductData[0]['description']) ?></p>
                        <p><strong>Price:</strong> $<?= htmlspecialchars($getProductData[0]['price']) ?></p>
                    </div>
                </div>
            </div>

            <div id="drag-drop-video-area" class="drag-drop-video-area p-5 text-center">
                <h4>Drag and Drop Your Video Here</h4>
                <p class="text-muted">Or click to select a video file.</p>
                <div id="drop-zone" class="drop-zone">
                    <p class="mb-4">Drag your video or click to browse</p>
                    <button class="btn btn-success" id="select-video-btn">Select Video</button>
                    <input type="file" id="fileInput" class="d-none" accept="video/*" multiple>
                </div>
                <div id="fileList" class="mt-4"></div>

     
                <div id="loader" class="d-none">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Uploading...</p>
                </div>
            </div>

            <div id="product-video-section" class="mt-5">
                <h5>Your Product Videos</h5>
                <div id="product-video-list" class="row">
                   
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>

<script>
$(document).ready(function() {
    const dropZone = $('#drop-zone');
    const fileInput = $('#fileInput');
    const fileList = $('#fileList');
    const loader = $('#loader');
    const productVideoList = $('#product-video-list');
    const selectVideoBtn = $('#select-video-btn');
    
    let videoFiles = [];


    dropZone.on('dragover', function(e) {
        e.preventDefault();
        dropZone.addClass('drag-over');
    });

    dropZone.on('dragleave', function() {
        dropZone.removeClass('drag-over');
    });

    dropZone.on('drop', function(e) {
        e.preventDefault();
        dropZone.removeClass('drag-over');
        const files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });


    fileInput.on('change', function(e) {
        const files = e.target.files;
        handleFiles(files);
    });


    function handleFiles(files) {
        if (videoFiles.length + files.length > 3) {
            alert("You can only upload a maximum of 3 videos.");
            return;
        }

        $.each(files, function(index, file) {
            if (file.type.startsWith('video')) {
                videoFiles.push(file);
            } else {
                alert("Only video files are allowed.");
                return;
            }
        });

        displayFiles();
    
        uploadVideos();
    }

    // Display selected files
    function displayFiles() {
        fileList.empty();
        $.each(videoFiles, function(index, file) {
            const listItem = $('<div>', { class: 'file-item mb-3 border p-3 rounded' })
                .append(`<strong>${file.name}</strong>`)
                .append(`<p>Size: ${Math.round(file.size / 1024)} KB</p>`)
                .append(`<video width="300" controls>
                            <source src="${URL.createObjectURL(file)}" type="${file.type}">
                            Your browser does not support the video tag.
                        </video>`);
            fileList.append(listItem);
        });
    }


    function uploadVideos() {
        if (videoFiles.length === 0) return;

        loader.removeClass('d-none');
        
        const formData = new FormData();
        $.each(videoFiles, function(index, file) {
            formData.append('videoFiles[]', file);
        });

        formData.append('product_id', <?= $product_id ?>);  

        $.ajax({
            url: '<?= $urlval ?>ajax/upload_video.php',
            type: 'POST',
            data: formData,
            processData: false,  
            contentType: false,  
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Videos uploaded successfully!');
                    displayProductVideos(result.videoPaths);
                } else {
                    alert('Error uploading videos.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Failed to upload videos.');
            },
            complete: function() {
                loader.addClass('d-none');  
            }
        });
    }

 
    function displayProductVideos(videoPaths) {
        productVideoList.empty();  

        $.each(videoPaths, function(index, path) {
            const videoElement = $('<div>', { class: 'col-4 mb-4' })
                .append(`<video width="300" controls>
                            <source src="${path}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>`);
            productVideoList.append(videoElement);
        });
    }

  
    function loadProductVideos() {
        $.ajax({
            url: '<?= $urlval ?>ajax/get_product_videos.php',
            type: 'GET',
            data: { product_id: <?= $product_id ?> },  
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    displayProductVideos(result.videoPaths);
                } else {
                    console.error('No videos found for this product.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    
    $('#upload-video-btn').on('click', function() {
        uploadVideos();
    });

  
    loadProductVideos();
});

</script>



</body>
</html>
