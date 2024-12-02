<?php
require_once("../global.php");
include_once('header.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $settingsArray = [];

    foreach ($_POST as $key => $value) {
        if($key == 'google_add_script'){
            $settingsArray[$key] = $value;
            
        }elseif($key == 'google_map_script'){
            $settingsArray[$key] = $value;

        }
        elseif($key == 'google_ads_txt'){
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/Ads.txt';

            if (file_put_contents($filePath, $value) !== false) {
                echo "Ads.txt updated successfully at $filePath.";
            } else {
                echo "Failed to update Ads.txt at $filePath.";
            }
            $settingsArray[$key] = htmlspecialchars($value);

        }
        else{

            $settingsArray[$key] = htmlspecialchars($value);
        }
    }

    if($settingsArray){
        $updateData=$fun->updateDatasiteseeting('site_settings',$settingsArray);
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
                    <button id="backupBtn" class="au-btn au-btn-icon au-btn--small" style="background-color: #28a745; color: white;" download>
                        <i class="zmdi zmdi-download"></i> Take Database Backup
                    </button>
                    </div>
                    
                <?php
                echo $fun->generateSettingsForm();
                
                ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
include_once('footer.php');
?>

<script>
document.getElementById('backupBtn').addEventListener('click', function() {
    if (confirm('Are you sure you want to take a backup of the database?')) {
        fetch('<?=$urlval?>admin/ajax/backup.php', { method: 'GET' })
            .then(response => response.blob())  // Receive the response as a blob
            .then(blob => {
                const link = document.createElement('a');
                const url = window.URL.createObjectURL(blob);
                link.href = url;  // Create a URL for the blob
                link.download = 'database_backup.sql'; // Name the file
                document.body.appendChild(link);
                link.click();  // Simulate a click to trigger the download
                link.remove(); // Clean up the link element
            })
            .catch(error => alert('Error: ' + error)); // Show an error if something goes wrong
    }
});
</script>