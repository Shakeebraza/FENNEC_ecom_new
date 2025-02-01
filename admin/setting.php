<?php
require_once("../global.php");
include_once('header.php');

// 1) Confirm the user’s session is valid, role is in [1,3,4]. 
//    (If your header.php already does that, you can skip this.)
$role = $_SESSION['arole'] ?? 0; 
if (!in_array($role, [1,3,4])) {
    // If the role is invalid, redirect or show error
    header("Location: {$urlval}admin/logout.php");
    exit;
}

// 2) Check if user is Admin or Super Admin
$isAdmin = in_array($role, [1,3]);

// 3) Handle POST only if $isAdmin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $isAdmin) {
    $settingsArray = [];

    foreach ($_POST as $key => $value) {
        if ($key == 'google_add_script' || $key == 'google_map_script') {
            $settingsArray[$key] = $value;  // allow script tags
        } elseif ($key == 'google_ads_txt') {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/Ads.txt';
            if (file_put_contents($filePath, $value) !== false) {
                echo "Ads.txt updated successfully at $filePath.";
            } else {
                echo "Failed to update Ads.txt at $filePath.";
            }
            $settingsArray[$key] = htmlspecialchars($value);
        } else {
            $settingsArray[$key] = htmlspecialchars($value);
        }
    }

    if ($settingsArray) {
        $updateData = $fun->updateDatasiteseeting('site_settings', $settingsArray);
        // var_dump($updateData);
    }
}
?>
<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid d-flex justify-content-center" style="min-height: 100vh;">
                <div class="row" style="width:100%;">
                    <div style="display: flex;justify-content: space-between;margin-bottom: 20px;">
                        <h3>Site Setting</h3>
                        <!-- Hide the backup button if user is NOT admin/super-admin -->
                        <?php if ($isAdmin): ?>
                        <button id="backupBtn" class="au-btn au-btn-icon au-btn--small"
                            style="background-color: #28a745; color: white;" download>
                            <i class="zmdi zmdi-download"></i> Take Database Backup
                        </button>
                        <?php endif; ?>
                    </div>

                    <?php
                    // If moderator, we might want to disable the form fields entirely.
                    // The generateSettingsForm() presumably creates an HTML form. 
                    // Let’s capture its HTML and disable inputs if not isAdmin.
                    
                    $formHtml = $fun->generateSettingsForm(); 
                    
                    if (!$isAdmin) {
                        // Add "disabled" to all <input>, <select>, <textarea> in the form
                        // Simple approach: do a string replace. 
                        // A robust approach would be to modify generateSettingsForm() itself.
                        $formHtml = preg_replace(
                            '/(<input\b[^>]*|<select\b[^>]*|<textarea\b[^>]*)>/i',
                            '$1 disabled>',
                            $formHtml
                        );
                        // Also remove or hide the <button type="submit"> if any
                        // Or you can add a note "Read-only for moderators"
                        $formHtml = preg_replace(
                            '/<button\b[^>]*>.*?<\/button>/is',
                            '<div class="alert alert-info">Read-only access for moderators.</div>',
                            $formHtml
                        );
                    }

                    echo $formHtml;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>

<script>
document.getElementById('backupBtn')?.addEventListener('click', function() {
    if (confirm('Are you sure you want to take a backup of the database?')) {
        fetch('<?=$urlval?>admin/ajax/backup.php', {
                method: 'GET'
            })
            .then(response => response.blob()) // Receive the response as a blob
            .then(blob => {
                const link = document.createElement('a');
                const url = window.URL.createObjectURL(blob);
                link.href = url; // Create a URL for the blob
                link.download = 'database_backup.sql'; // Name the file
                document.body.appendChild(link);
                link.click(); // Simulate a click to trigger the download
                link.remove(); // Clean up
            })
            .catch(error => alert('Error: ' + error));
    }
});
</script>