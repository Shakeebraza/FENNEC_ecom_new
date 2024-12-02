<?php
require_once("../../global.php");
include_once('../header.php');

if (!isset($_GET['menuid'])) {
    echo "<script>
            alert('Invalid menu ID.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}


$menuid = $security->decrypt($_GET['menuid']);
$menu = $dbFunctions->getDataById('menus', $menuid); 


if (!$menu) {
    echo "<script>
            alert('Menu not found.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}
$subMenus = $dbFunctions->getData('menu_items',"menu_id ='$menuid'");

?>
<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="container form-container">
                        <h1>Edit Sub Menu</h1>

                        <?php if (isset($error)): ?>
                            <div class="error"><?= $security->decrypt($error) ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <!-- Form Column -->
                            <div class="col-md-6">
                            <form id="editSubMenuForm">
                                <div class="form-group">
                                    <label for="heading">Name</label>
                                    <input type="text" id="heading" name="heading" class="form-control" value="" required>
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <input type="text" id="slug" name="slug" class="form-control" value="" required readonly>
                                </div>

                                <div class="form-group">
                                    <label for="link">Link</label>
                                    <input type="url" id="link" name="link" class="form-control" value="">
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control" required>
                                        <option disabled value="">Select Status</option>
                                        <option value="1">Activate</option>
                                        <option value="0">Decline</option>
                                    </select>
                                </div>
                                <input type="hidden" id="menuid" name="menuid" value="<?= $security->encrypt($menuid)?>">
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa fa-dot-circle-o"></i> Submit
                                    </button>
                                </div>
                            </form>

                            </div>

                            <!-- Table Column -->
                            <div class="col-md-6">
                                <h2>Existing Sub Menus</h2>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subMenus as $subMenu): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($security->decrypt($subMenu['name'])) ?></td>
                                            <td>
                                                <a href="<?= $urlval?>admin/menu/edit_sub_menu.php?menuid=<?= $security->decrypt($subMenu['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $security->decrypt($subMenu['id'])?>">Delete</button>

                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
include_once('../footer.php');
?>
<script src="<?php echo $urlval?>admin/asset/js/textaera.js"></script>
<script>
$(document).ready(function() {
    $('#editSubMenuForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            heading: $('#heading').val(),
            slug: $('#slug').val(),
            link: $('#link').val(),
            status: $('#status').val(),
            menuid: $('#menuid').val(),
        };

        $.ajax({
            url: '<?php echo $urlval?>admin/ajax/menu/addsubmenu.php', 
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Submenu updated successfully.');
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    });
});

$(document).ready(function(){
    $('.delete-btn').click(function(){
        const menuId = $(this).data('id'); 

        if(confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: '<?php echo $urlval?>admin/ajax/menu/delete_menu.php', 
                type: 'POST',
                data: { id: menuId },
                success: function(response) {
                    
                    const res = JSON.parse(response);
                    if(res.success) {
                        alert('Record deleted successfully.');
                       
                        location.reload();
                    } else {
                        alert('Error deleting record: ' + res.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    alert('An error occurred while deleting the record.');
                }
            });
        }
    });
});

document.getElementById('heading').addEventListener('input', function() {
    const slugField = document.getElementById('slug');
    const slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, ''); 
    slugField.value = slug;
});
</script>

</body>
</html>
