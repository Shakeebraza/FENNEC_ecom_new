<?php
require_once "../global.php";

$logFile = "../logs/pending_ads.log"; // Define log file path

function logMessage($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

logMessage("---- New Request ----");

// Only proceed if an admin user session exists
if (isset($_SESSION['auserid'])) {
    $userid = base64_decode($_SESSION['auserid']);
    logMessage("Admin User ID: $userid is fetching pending ads count.");

    try {
        // Query: count of products with is_enable=2 (i.e., "pending" ads)
        $query = "SELECT COUNT(id) AS pending_count FROM products WHERE is_enable = 2";
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        $pending_count = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pending_count = $row['pending_count'];
        }

        logMessage("Pending Ads Count: $pending_count");
        echo json_encode(['pending_count' => $pending_count]);
    } catch (Exception $e) {
        logMessage("ERROR: " . $e->getMessage());
        echo json_encode(['error' => 'Database error. Please try again later.']);
    }
} else {
    logMessage("Unauthorized access attempt.");
    echo json_encode(['error' => 'Unauthorized access.']);
}
?>
