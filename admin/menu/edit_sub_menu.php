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


$menuid = $_GET['menuid'];

$menu = $dbFunctions->getDataById('menu_items', $menuid); 


if (!$menu) {
    echo "<script>
            alert('Menu not found.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading = $_POST['heading'];
    $slug = $_POST['slug'];
    $link = $_POST['link'];
    $status = $_POST['status'];
    $menuid = $security->decrypt($_POST['menuid']);

    $updateData = [
        'name' => $heading,
        'slug' => $slug,
        'link' => $link,
        'is_enable' => $status,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $updateResult = $dbFunctions->setData('menu_items', $updateData, ['id' => $menuid]);
   
    if ($updateResult['success']) {
        echo "<script>alert('Menu updated successfully.');</script>";
    } else {
        echo "<script>alert('Error updating menu: {$updateResult['message']}');</script>";
    }
}

?>
<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="container form-container">
                        <h1>Edit Menu</h1>

                        <?php if (isset($error)): ?>
                            <div class="error"><?= $security->decrypt($error) ?></div>
                        <?php endif; ?>

                        <form method="POST">
                                <div class="form-group">
                                    <label for="heading">Name</label>
                                    <input type="text" id="heading" name="heading" class="form-control" value="<?= $security->decrypt($menu['name'])?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <input type="text" id="slug" name="slug" class="form-control" value="<?= $security->decrypt($menu['slug']) ?>" required readonly>
                                </div>

                                <div class="form-group">
                                    <label for="link">Link</label>
                                    <input type="url" id="link" name="link" class="form-control" value="<?= $security->decrypt($menu['link']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control" required>
                                        <option disabled value="">Select Status</option>
                                        <option value="1" <?= isset($menu['is_enable']) && $security->decrypt($menu['is_enable']) == "1" ? 'selected' : '' ?>>Activate</option>
                                        <option value="0" <?= isset($menu['is_enable']) && $security->decrypt($menu['is_enable']) == "0" ? 'selected' : '' ?>>Decline</option>
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
