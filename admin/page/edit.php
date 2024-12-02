<?php
require_once("../../global.php");
include_once('../header.php');
if (!isset($_GET['pageid'])) {
    echo "<script>
            alert('Invalid page ID.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}


$pageid = $security->decrypt($_GET['pageid']);
$page = $dbFunctions->getDataById('pages', $pageid); 
if (!$page) {
    echo "<script>
            alert('page not found.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $heading = $_POST['heading'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $link = $_POST['link'] ?? '';
    $description = $_POST['description'] ?? '';
    $text_area = $_POST['textaera'] ?? '';
    $status = $_POST['status'] ?? '';

    $addNewData = [
        'name' => htmlspecialchars($heading, ENT_QUOTES, 'UTF-8'), 
        'slug' => htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'),
        'link' => htmlspecialchars($link, ENT_QUOTES, 'UTF-8'),
        'content' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'), 
        'subcontent' => $text_area,
        'is_enable' => $status,
    ];


    $updateResult = $dbFunctions->setDataWithHtmlAllowed('pages', $addNewData,['id' => $pageid]); 

    if ($updateResult['success']) {
        echo "<script>alert('Page added successfully.');</script>";
    } else {
        echo "<script>alert('Error adding page: {$updateResult['message']}');</script>";
    }
}



?>
<style>

        svg {
            display: none;
        }

</style>



<div class="page-container">
        <div class="main-content">
            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="row">
                        <div class="container form-container">
                            <h1>Edit Page</h1>

                            <?php if (isset($error)): ?>
                                <div class="error"><?= $security->decrypt($error) ?></div>
                            <?php endif; ?>

                            <form method="POST" enctype="multipart/form-data">
                                
                                <div class="form-group">
                                    <label for="heading">Name</label>
                                    <input type="text" id="heading" name="heading" class="form-control"
                                        value="<?= $security->decrypt($page['name'])?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <input type="text" id="slug" name="slug" class="form-control" value="<?= $security->decrypt($page['slug'])?>" required readonly>
                                </div>

                                <div class="form-group">
                                    <label for="link">Link</label>
                                    <input type="url" id="link" name="link" class="form-control" value="<?= $security->decrypt($page['link'])?>">
                                </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" class="form-control" cols="30" rows="5"><?= $security->decrypt($page['content'])?></textarea>
                                    </div>


                             
                                    <div class="form-group">
                                        <label for="textaera">Content</label>
                                        <textarea name="textaera" id="default" class="form-control" cols="30" rows="10"><?= $security->decrypt($page['subcontent'])?></textarea>
                                    </div>
                      
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select id="status" name="status" class="form-control">
                                            <option disabled value="">Select Status</option>
                                            <option value="1" <?= isset($page['is_enable']) && $security->decrypt($page['is_enable']) == 1 ? 'selected' : '' ?>>Activate</option>
                                            <option value="0" <?= isset($page['is_enable']) && $security->decrypt($page['is_enable']) == 0 ? 'selected' : '' ?>>Decline</option>
                                        </select>
                                    </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa fa-dot-circle-o"></i> Submit
                                    </button>
                                    <a href="<?= $urlval?>admin/page/index.php" type="reset" class="btn btn-danger btn-sm">
                                        <i class="fa fa-ban"></i> Back
                                    </a>
                                </div>
                            </form>
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