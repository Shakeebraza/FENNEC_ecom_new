<?php
require_once("../../global.php");
include_once('../header.php');
?>

<div class="page-container">

<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="title-5 m-b-35">Manage Banners</h3>
                    <form id="userSearchForm">
                        <div class="form-row searchfromwhite">
                            <div class="form-group col-md-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="status">Status</label>
                                <select class="form-control" id="status">
                                    <option value="" selected>All Statuses</option>
                                    <option value="1">Activated</option>
                                    <option value="0">Unactivated</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-success" id="searchMenu" style="height: 37px;margin-top: 30px;">Search</button>
                        </div>
                    </form>
                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Image</th>
                                    <th>Show as</th>
                                    <th>Get code</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>



<div id="popupModal" class="popup-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); display: none; align-items: center; justify-content: center; z-index: 1000; transition: opacity 0.3s ease-in-out;">
    <div class="popup-content" style="background-color: white; padding: 20px; width: 80%; max-width: 800px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); overflow-y: auto;">
        <div class="header" style="font-size: 24px; font-weight: bold; margin-bottom: 10px; text-align: center;">
            Get Code
        </div>
        <div class="content">
            <p class="instruction" style="font-size: 16px; color: #555; margin-bottom: 20px; text-align: center;">
                Copy any of the following codes and paste it as it is on a webpage where you want to display the banners of that size.
            </p>

            <!-- Banner Details -->
            <div style="margin-bottom: 20px; text-align: center;">
                <div id="bannerTitle" style="font-size: 20px; font-weight: bold;"></div>
                <div id="bannerDescription" style="font-size: 16px; color: #555; margin: 10px 0;"></div>
                <div id="bannerImage" style="margin-bottom: 20px;">
                    <img id="bannerImageUrl" src="" alt="Banner Image" style="max-width: 100%; height: auto;">
                </div>
            </div>

            <!-- iFrame Embedding Code for Different Sizes -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <div class="size-section" style="flex: 1; margin: 0 10px;">
                    <div class="size-label" style="font-weight: bold; margin-bottom: 5px; display: block;">Size: 468x60 px</div>
                    <textarea readonly id="iframeCode468" style="width: 100%; height: 80px; padding: 10px; font-size: 14px; border: 1px solid #ddd; border-radius: 5px; resize: none; box-sizing: border-box;"></textarea>
                </div>

                <div class="size-section" style="flex: 1; margin: 0 10px;">
                    <div class="size-label" style="font-weight: bold; margin-bottom: 5px; display: block;">Size: 234x60 px</div>
                    <textarea readonly id="iframeCode234" style="width: 100%; height: 80px; padding: 10px; font-size: 14px; border: 1px solid #ddd; border-radius: 5px; resize: none; box-sizing: border-box;"></textarea>
                </div>
            </div>

            <button class="close-popup" onclick="closePopup()" style="display: block; width: 100%; background-color: #f44336; color: white; padding: 12px; font-size: 16px; font-weight: bold; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; text-align: center; transition: background-color 0.3s ease;">
                Close
            </button>
        </div>
    </div>
</div>


<?php
include_once('../footer.php');
?>
<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/banner/fetchbanner.php",
            "type": "POST",
            "data": function(d) {
                d.name = $('#name').val();  
                d.status = $('#status').val();  
            }
        },
        "columns": [
            {"data": "checkbox"},
            {"data": "name"},
            {"data": "date"},
            {"data": "image"},
            {"data": "show"},
            {"data": "get_code"},
            {"data": "status"},
            {"data": "actions"}
        ],
    });

    $('#searchMenu').on('click', function() {
        table.draw();
    });

    $(document).on('click', '.delete-banner', function() {
        var bannerId = $(this).data('id');
        var confirmDelete = confirm('Are you sure you want to delete this banner?');

        if (confirmDelete) {
            $.ajax({
                url: '<?php echo $urlval; ?>admin/ajax/banner/deletebanner.php',
                type: 'POST',
                data: { id: bannerId },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert('Banner deleted successfully');
                        location.reload();
                    } else {
                        alert('Failed to delete banner');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Error occurred. Please try again.');
                }
            });
        }
    });

    $(document).on('click', '.get-code', function() {
        var bannerId = $(this).data('id'); 
        openPopup(bannerId); 
    });
});

function openPopup(bannerId) {
    $.ajax({
        url: "<?php echo $urlval; ?>admin/ajax/banner/getBannerData.php",
        type: "POST",
        data: { id: bannerId },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                var banner = response.data;

                // Set banner details
                document.getElementById("bannerTitle").textContent = banner.title;
                document.getElementById("bannerDescription").textContent = banner.description;
                document.getElementById("bannerImageUrl").src = banner.image;

                // Display or hide image container based on availability
                document.getElementById("bannerImage").style.display = banner.image ? "block" : "none";

                // Generate HTML for the two sizes
                var html468 = `
                    <div style="width: 468px; height: 60px; background-color: ${banner.bg_color}; color: ${banner.text_color}; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd;">
                        <img src="${banner.image}" alt="Banner Image" style="max-height: 100%; max-width: 100%;">
                        <div style="position: absolute; text-align: center;">
                            <h1 style="font-size: 16px; margin: 0;">${banner.title}</h1>
                            <p style="font-size: 14px; margin: 0;">${banner.description}</p>
                        </div>
                    </div>
                `;
                var html234 = `
                    <div style="width: 234px; height: 60px; background-color: ${banner.bg_color}; color: ${banner.text_color}; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd;">
                        <img src="${banner.image}" alt="Banner Image" style="max-height: 100%; max-width: 100%;">
                        <div style="position: absolute; text-align: center;">
                            <h1 style="font-size: 14px; margin: 0;">${banner.title}</h1>
                            <p style="font-size: 12px; margin: 0;">${banner.description}</p>
                        </div>
                    </div>
                `;

                // Set HTML into the respective textareas
                document.getElementById("iframeCode468").value = html468;
                document.getElementById("iframeCode234").value = html234;

                // Show the modal popup
                document.getElementById("popupModal").style.display = "flex";
            } else {
                alert("Error fetching banner data");
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
            alert("An error occurred while fetching the banner data.");
        },
    });
}

function closePopup() {
    document.getElementById("popupModal").style.display = "none";
}







</script>

</body>

</html>