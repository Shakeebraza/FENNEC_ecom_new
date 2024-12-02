<?php
require_once("../../global.php");
include_once('../header.php');

// Check if boost ID is provided
if (!isset($_GET['boostid'])) {
    echo "<script>
            alert('Invalid boost ID.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}

$boostId = base64_decode($_GET['boostid']);
$boost = $dbFunctions->getDataById('boost_plans', $boostId);

if (!$boost) {
    echo "<script>
            alert('Boost not found.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $price = $_POST['price'] ?? '';
    $benefits = $_POST['benefits'] ?? '';
    $status = $_POST['status'] ?? 'inactive';

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $fun->uploadImage($_FILES['image']);
    }

    // Prepare data for update
    $updateData = [
        'name' => $name,
        'description' => $description,
        'duration' => $duration,
        'price' => $price,
        'benefits' => $benefits,
    ];

    if ($image) {
        $updateData['image'] = $image;
    }

    // Update boost data
    $updateResult = $dbFunctions->setData('boost_plans', $updateData, ['id' => $boostId]);
    if ($updateResult['success']) {
        echo "<script>alert('Boost updated successfully.'); window.location.href='".$urlval."admin/packages/index.php';</script>";
    } else {
        echo "<script>alert('Error updating boost: {$updateResult['message']}');</script>";
    }
}
?>

<div class="page-container">
        <div class="main-content">
            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="row">
                        <div class="container form-container">
                            <h1>Edit Boost</h1>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" class="form-control" 
                                        value="<?= htmlspecialchars($security->decrypt($boost['name'])) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="5"><?= htmlspecialchars($security->decrypt($boost['description'])) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="duration">Duration (in days)</label>
                                    <input type="number" id="duration" name="duration" class="form-control" 
                                        value="<?= htmlspecialchars($security->decrypt($boost['duration'])) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="text" id="price" name="price" class="form-control" 
                                        value="<?= htmlspecialchars($security->decrypt($boost['price'])) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="benefits">Benefits</label>
                                    <textarea id="benefits" name="benefits" class="form-control" rows="4"><?= htmlspecialchars($security->decrypt($boost['benefits'])) ?></textarea>
                                </div>


                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </form>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>

<?php include_once('../footer.php'); ?>
