<?php
require_once 'global.php';
include_once 'header.php';

// Search query ko check karen
$query = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : null;
?>

<div style="text-align: center; margin: 50px auto;">
    <h1 style="font-size: 2.5rem; color: #333;">0 Results for "<?php echo $query; ?>"</h1>
    <p style="font-size: 1.2rem; color: #555; margin-top: 10px;">
        Check the spelling<br>
        Try adding or removing words<br>
        Try browsing the categories<br>
    </p>

    <p style="font-size: 1.1rem; color: #555; margin-top: 20px;">
        Save this search to receive email alerts and notifications when new items are available.
    </p>

    <a href="<?= $urlval?>index.php" style="text-decoration: none; display: inline-block; margin-top: 20px;">
        <button style="padding: 10px 20px; font-size: 1rem; background-color: #00494f; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
            Back to Home
        </button>
    </a>
</div>

<?php include_once 'footer.php'; ?>
