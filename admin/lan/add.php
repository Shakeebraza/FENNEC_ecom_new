<?php
require_once("../../global.php");
include_once('../header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language_code = $_POST['language_code'] ?? '';
    $language_name = $_POST['language_name'] ?? '';

    $file = $_FILES['file'] ?? null;
    $filePath = '';

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $filePath = $fun->uploadLanFiles($file);
    }


    if (!$filePath) {
        echo "<script>alert('Error: Please upload a valid PHP language file.');</script>";
        return;
    }


    $addNewData = [
        'language_code' => $language_code,
        'language_name' => $language_name,
        'file_path' => $filePath,
    ];


    $updateResult = $dbFunctions->setData('languages', $addNewData);

    if ($updateResult['success']) {
        echo "<script>alert('Language uploaded successfully.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error uploading language: {$updateResult['message']}');</script>";
    }
}



?>
<style>


.card {
    border-radius: 10px;
}

.card-header {
    border-bottom: 2px solid #eee;
}

.btn-success {
    background-color: #28a745;
    border: none;
    transition: background-color 0.3s;
}

.btn-success:hover {
    background-color: #218838;
}

.form-label {
    font-weight: bold;
}

input[type="text"], input[type="file"] {
    border-radius: 5px;
}

.mt-5 {
    margin-top: 3rem !important;
}
</style>



<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card shadow-sm mt-5">
                            <div class="card-header bg-primary text-white">
                                <h2 class="text-center">Upload New Language</h2>
                            </div>
                            <div class="card-body">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger">
                                        <?= $security->decrypt($error) ?>
                                    </div>
                                <?php endif; ?>

                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="language_code" class="form-label">Language Code (e.g., en, ur):</label>
                                        <input type="text" class="form-control" id="language_code" name="language_code" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="language_name" class="form-label">Language Name:</label>
                                        <input type="text" class="form-control" id="language_name" name="language_name" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="file" class="form-label">Select Language File (.php):</label>
                                        <input type="file" class="form-control" id="file" name="file" accept=".php" required>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">Upload Language</button>
                                </form>

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

<script>

</script>

</body>

</html>