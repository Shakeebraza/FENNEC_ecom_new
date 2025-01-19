<?php
require_once 'global.php';
include_once 'header.php';
$redirectUrl = $urlval . 'index.php';

// Validate product ID
if (!isset($_GET['productid'])) {
    echo '<script>window.location.href = "' . $redirectUrl . '";</script>';
    exit();
}

$productId = base64_decode($_GET['productid']);
$getproduct = $dbFunctions->getDatanotenc('products', "id='$productId'");

if (empty($getproduct[0])) {
    echo '<script>window.location.href = "' . $redirectUrl . '";</script>';
    exit();
}

$userId = intval(base64_decode($_SESSION['userid'])); // Get user ID from session

// Fetch wallet balance
$stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$userBalance = $stmt->fetchColumn();

if ($userBalance === false) {
    echo '<script>alert("Unable to retrieve wallet balance. Please try again."); window.location.href = "' . $redirectUrl . '";</script>';
    exit();
}

// Fetch boost plans
$boostPlans = $fun->getBoostPlans();
if (!$boostPlans || !is_array($boostPlans)) {
    echo '<script>alert("No boost plans available. Please try again later."); window.location.href = "' . $redirectUrl . '";</script>';
    exit();
}
?>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        gap: 20px;
        flex-wrap: wrap;
        padding: 20px;
    }

    .boost-container {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        padding: 40px;
        width: 48%;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .boost-container:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
    }

    h2 {
        font-size: 28px;
        color: #3a3a3a;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .boost-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
    }

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
        content: "\2713";
        color: #28a745;
        position: absolute;
        left: 0;
        font-size: 18px;
        top: 50%;
        transform: translateY(-50%);
    }

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

    .btn2:hover {
        background-color: #b38e1a;
        transform: translateY(-3px);
    }
</style>

<div class="container">
    <div class="plans-row">
        <?php foreach ($boostPlans as $plan): ?>
            <div class="boost-container <?php echo strtolower($plan['slug']); ?>-plan">
                <h2><?php echo htmlspecialchars($plan['name']); ?></h2>
                <div class="boost-image">
                    <img src="<?php echo htmlspecialchars($urlval . $plan['image']); ?>" alt="<?php echo htmlspecialchars($plan['name']); ?>">
                </div>
                <p><?php echo htmlspecialchars($plan['description']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($plan['price'], 2); ?></p>
                <ul class="boost-benefits">
                    <?php 
                    $benefits = explode(',', $plan['benefits']);
                    foreach ($benefits as $benefit): ?>
                        <li><?php echo htmlspecialchars(trim($benefit)); ?></li>
                    <?php endforeach; ?>
                </ul>

                <button type="button" class="btn2" onclick="confirmBoostPlan('<?php echo $plan['slug']; ?>', '<?php echo $plan['id']; ?>', '<?php echo $plan['price']; ?>')">Activate <?php echo htmlspecialchars($plan['name']); ?></button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function confirmBoostPlan(boostType, planId, price) {
    Swal.fire({
        title: 'Confirm Activation',
        text: `This plan will be charged $${price} from your wallet. Do you want to proceed?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Activate!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Perform the wallet deduction and activation
            fetch('boost_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    boostType: boostType,
                    planId: planId,
                    price: price,
                    productId: '<?php echo $productId; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success', data.message, 'success').then(() => {
                        window.location.href = "index.php";
                    });
                } else if (data.message === 'Plan is already active for this product.') {
                    Swal.fire('Info', 'This plan is already active for the selected product.', 'info');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'An error occurred. Please try again.', 'error');
            });
        }
    });
}

</script>
