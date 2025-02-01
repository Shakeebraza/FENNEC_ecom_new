<?php
require_once("../../global.php");
include_once('../header.php');

// Roles: [1 => Super Admin, 3 => Admin, 4 => Moderator]
$role    = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1,3]);
?>
<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="title-5 m-b-35">Manage Banners</h3>

                        <?php if (!$isAdmin): ?>
                        <p>You have read-only access to the banners.</p>
                        <?php else: ?>
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
                                <button type="button" class="btn btn-success" id="searchMenu"
                                    style="height:37px; margin-top:30px;">
                                    Search
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>

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
                                <tbody>
                                    <!-- DataTables will fill -->
                                </tbody>
                            </table>
                        </div><!-- table-responsive -->
                    </div><!-- col-md-12 -->
                </div><!-- row -->
            </div><!-- container-fluid -->
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<!-- Popup for “Get Code” -->
<div id="popupModal" class="popup-modal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7);
            display:none; align-items:center; justify-content:center; z-index:1000;">
    <div class="popup-content" style="background-color:white; padding:20px; width:80%; max-width:800px;
         border-radius:10px; overflow-y:auto;">
        <div class="header" style="font-size:24px; font-weight:bold; margin-bottom:10px; text-align:center;">
            Get Code
        </div>
        <div class="content">
            <p class="instruction" style="font-size:16px; color:#555; margin-bottom:20px; text-align:center;">
                Copy the following code(s) where you want the banner to appear.
            </p>
            <div style="margin-bottom:20px; text-align:center;">
                <div id="bannerTitle" style="font-size:20px; font-weight:bold;"></div>
                <div id="bannerDescription" style="font-size:16px; color:#555; margin:10px 0;"></div>
                <div id="bannerImage" style="margin-bottom:20px;">
                    <img id="bannerImageUrl" src="" alt="Banner" style="max-width:100%; height:auto;">
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                <div style="flex:1; margin:0 10px;">
                    <div style="font-weight:bold; margin-bottom:5px;">468x60 px</div>
                    <textarea readonly id="iframeCode468"
                        style="width:100%; height:80px; border:1px solid #ddd; border-radius:5px;"></textarea>
                </div>
                <div style="flex:1; margin:0 10px;">
                    <div style="font-weight:bold; margin-bottom:5px;">234x60 px</div>
                    <textarea readonly id="iframeCode234"
                        style="width:100%; height:80px; border:1px solid #ddd; border-radius:5px;"></textarea>
                </div>
            </div>
            <button class="close-popup" onclick="closePopup()" style="display:block; width:100%; background-color:#f44336; color:white; padding:12px; font-size:16px;
                           font-weight:bold; border:none; border-radius:5px; cursor:pointer; margin-top:20px;">
                Close
            </button>
        </div>
    </div>
</div>

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function() {
    var canEdit = <?php echo $isAdmin ? 'true' : 'false'; ?>;

    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/banner/fetchbanner.php",
            "type": "POST",
            "data": function(d) {
                d.name = $('#name').val();
                d.status = $('#status').val();
            }
        },
        "columns": [{
                "data": "checkbox"
            },
            {
                "data": "name"
            },
            {
                "data": "date"
            },
            {
                "data": "image"
            },
            {
                "data": "show"
            },
            {
                "data": "get_code"
            },
            {
                "data": "status"
            },
            {
                "data": "actions"
            }
        ]
    });

    $('#searchMenu').on('click', function() {
        table.draw();
    });

    // If moderator => no delete logic
    if (canEdit) {
        $(document).on('click', '.delete-banner', function() {
            var bannerId = $(this).data('id');
            if (confirm('Are you sure you want to delete this banner?')) {
                $.post(
                    '<?php echo $urlval; ?>admin/ajax/banner/deletebanner.php', {
                        id: bannerId
                    },
                    function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            alert('Banner deleted successfully');
                            table.ajax.reload();
                        } else {
                            alert('Failed to delete banner: ' + (result.message || 'Unknown'));
                        }
                    }
                ).fail(function() {
                    alert('Error occurred. Please try again.');
                });
            }
        });
    }

    $(document).on('click', '.get-code', function() {
        var bannerId = $(this).data('id');
        openPopup(bannerId);
    });
});

function openPopup(bannerId) {
    $.ajax({
        url: "<?php echo $urlval; ?>admin/ajax/banner/getBannerData.php",
        type: "POST",
        data: {
            id: bannerId
        },
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                var banner = response.data;
                $("#bannerTitle").text(banner.title);
                $("#bannerDescription").text(banner.description);
                $("#bannerImageUrl").attr("src", banner.image);
                document.getElementById("bannerImage").style.display = banner.image ? "block" : "none";

                var html468 = `
                    <div style="width:468px; height:60px; background-color:${banner.bg_color};
                                color:${banner.text_color}; display:flex; align-items:center;
                                justify-content:center; border:1px solid #ddd; position:relative;">
                        <img src="${banner.image}" alt="Banner"
                             style="max-height:100%; max-width:100%;">
                        <div style="position:absolute; text-align:center;">
                            <h1 style="font-size:16px; margin:0;">${banner.title}</h1>
                            <p style="font-size:14px; margin:0;">${banner.description}</p>
                        </div>
                    </div>`;
                var html234 = `
                    <div style="width:234px; height:60px; background-color:${banner.bg_color};
                                color:${banner.text_color}; display:flex; align-items:center;
                                justify-content:center; border:1px solid #ddd; position:relative;">
                        <img src="${banner.image}" alt="Banner"
                             style="max-height:100%; max-width:100%;">
                        <div style="position:absolute; text-align:center;">
                            <h1 style="font-size:14px; margin:0;">${banner.title}</h1>
                            <p style="font-size:12px; margin:0;">${banner.description}</p>
                        </div>
                    </div>`;

                $("#iframeCode468").val(html468);
                $("#iframeCode234").val(html234);

                $("#popupModal").css("display", "flex");
            } else {
                alert("Error fetching banner data");
            }
        },
        error: function() {
            alert("An error occurred while fetching the banner data.");
        }
    });
}

function closePopup() {
    $("#popupModal").css("display", "none");
}
</script>
</body>

</html>