<?php
require_once 'global.php';
include_once 'header.php'; // Include the header for consistency (adjust if necessary)
if(!isset($_SESSION['userid'])){
    header('Location: index.php');
    exit();
}
?>

<style>
.success-message {
    font-size: 18px;
    color: green;
    text-align: center;
    margin-top: 50px;
}

.buttons-container {
    text-align: center;
    margin-top: 30px;
}

.buttons-container a {
    padding: 10px 20px;
    margin: 10px;
    background-color: #00494f;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
}

.buttons-container a:hover {
    background-color: #006666;
}
</style>

<div class="container" style="padding-bottom: 58px;">
    <div class="success-message">
        <!-- This message can be dynamically passed or retrieved -->
        <h2>Your product was successfully submitted!</h2>
    </div>

    <div class="buttons-container">
        <!-- Button to add new product -->
        <a href="<?=$urlval?>post.php">Add a new product</a>

        <!-- Button to go back to the home page -->
        <a href="<?=$urlval?>index.php">Go back to home</a>
    </div>
</div>

<?php
include_once 'footer.php'; // Include the footer for consistency (adjust if necessary)
?>

<script>
// Optionally, you can add any custom JavaScript here if required
</script>

</body>

</html>