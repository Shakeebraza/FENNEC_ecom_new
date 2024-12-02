<?php
require_once 'global.php';
include_once 'header.php';
$redirectUrl = $urlval . 'index.php';
if (!isset($_GET['productid'])) {
    echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>'; 
    exit();
}


$productId = base64_decode($_GET['productid']);
$getproduct = $dbFunctions->getDatanotenc('products',"id='$productId'");

if(empty($getproduct[0])){
    echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>'; 
    exit();
}
?>

<style>

    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 0;
    }


    .plans-row {
        display: flex;
        justify-content: space-between;
        gap: 20px; /* Space between plans */
        flex-wrap: wrap; /* Allow plans to wrap on smaller screens */
        padding: 20px;
    }

    /* Each individual plan container */
    .boost-container {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        padding: 40px;
        width: 48%; /* Ensure plans take up half of the available space */
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* Hover effect for the plan containers */
    .boost-container:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
    }

    /* Heading Styles */
    h2 {
        font-size: 28px;
        color: #3a3a3a;
        margin-bottom: 20px;
        font-weight: bold;
    }

    /* Image Styles */
    .boost-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
    }

    /* List Style for Benefits */
    .boost-benefits {
        list-style-type: none;
        padding: 0;
        margin-top: 20px;
    }

    .boost-benefits li {
        margin: 12px 0;
        padding-left: 20px;
        position: relative;
        font-size: 16px;
        color: #555;
    }

    .boost-benefits li::before {
        content: "✔";
        color: #28a745;
        position: absolute;
        left: 0;
        font-size: 18px;
        top: 50%;
        transform: translateY(-50%);
    }

    /* Button Styling */
    .btn2 {
        background-color: #d4af37;
        color: white;
        border: none;
        padding: 15px 25px;
        font-size: 18px;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        margin-top: 20px;
        transition: background-color 0.3s, transform 0.3s;
    }

    .btn:hover {
        background-color: #b38e1a;
        transform: translateY(-3px);
    }

    /* Plan Details Styling */
    .gold-plan {
        border-left: 5px solid #d4af37;
    }

    .premium-plan {
        border-left: 5px solid #007bff;
    }

    /* Small Text Styling */
    p {
        font-size: 16px;
        color: #777;
        margin: 20px 0;
    }

    /* Form Styling */
    form {
        display: inline-block;
        width: 100%;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .boost-container {
            width: 100%; /* Make each plan take full width on smaller screens */
        }
    }
</style>

<!-- Plans Row Container -->
<?php
$boostPlans = $fun->getBoostPlans();
?>

<div class="plans-row">
    <?php foreach ($boostPlans as $plan): ?>
    <div class="boost-container <?php echo strtolower($plan['slug']); ?>-plan">
        <h2><?php echo $plan['name']; ?></h2>
        <div class="boost-image">
            <img src="<?php echo $urlval . $plan['image']; ?>" alt="<?php echo $plan['name']; ?>">
        </div>
        <p><?php echo $plan['description']; ?></p>
        <p><strong>Price:</strong> $<?php echo number_format($plan['price'], 2); ?></p>
        <ul class="boost-benefits">
            <?php 
            $benefits = explode(',', $plan['benefits']);
            foreach ($benefits as $benefit): 
            ?>
            <li><?php echo trim($benefit); ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- Form to redirect to the payment page -->
        <form method="POST" action="payment.php">
            <input type="hidden" name="boost_type" value="<?php echo $plan['slug']; ?>">
            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
            <input type="hidden" name="price" value="<?php echo $plan['price']; ?>">
            <input type="hidden" name="plan_name" value="<?php echo $plan['name']; ?>">
            <input type="hidden" name="proid" value="<?php echo $productId; ?>">
            <button type="submit" class="btn2">Activate <?php echo $plan['name']; ?></button>
        </form>
    </div>
    <?php endforeach; ?>
</div>




<?php
include_once 'footer.php';
?>

<script>
// Additional JS functionality (if needed) can be added here.
</script>

</body>
</html>
