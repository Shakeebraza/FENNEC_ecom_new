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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading = $_POST['heading'] ?? '';
    $status = $_POST['status'] ?? '';


    $updateData = [
        'name' => $heading,
        'is_enabled' => $status,
        'updated_at'=> date('Y-m-d H:i:s'),
    ];


    $updateResult = $dbFunctions->setData('menus', $updateData, ['id' => $menuid]);

    if ($updateResult['success']) {
        echo "<script>alert('Menu updated successfully.');</script>";
        echo "<script>setTimeout(() => { window.location.href = '".$urlval."admin/index.php'; }, 2000);</script>"; // Redirect after 2 seconds
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
                                <input type="text" id="heading" name="heading" class="form-control"
                                    value="<?= htmlspecialchars($security->decrypt($menu['name'])) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option disabled value="">Select Status</option>
                                    <option value="1" <?= isset($menu['is_enabled']) && $menu['is_enabled'] == 1 ? 'selected' : '' ?>>Activate</option>
                                    <option value="0" <?= isset($menu['is_enabled']) && $menu['is_enabled'] == 0 ? 'selected' : '' ?>>Decline</option>
                                </select>
                            </div>

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
// Any additional JS can go here
</script>

</body>
</html>
