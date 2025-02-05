<?php
require_once("../../global.php");
include_once("../header.php");

// Role check (admins only)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

// Define log file path.
$logFile = __DIR__ . '/sharingcode.log';
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

$csrfError = '';
$message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
        logMessage("CSRF token validation failed.");
    } else {
        // Get the raw code input (do not trim or apply filtering)
        $code = $_POST['code'] ?? '';

        // Check if a record already exists in sharing_code (assuming a single record with id = 1)
        $stmt = $pdo->prepare("SELECT id FROM sharing_code WHERE id = 1");
        $stmt->execute();
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRecord) {
            // Update the existing record
            $updateStmt = $pdo->prepare("UPDATE sharing_code SET code = ?, updated_at = ? WHERE id = 1");
            $result = $updateStmt->execute([$code, date("Y-m-d H:i:s")]);
            if ($result) {
                $message = "Sharing code updated successfully.";
                logMessage("Sharing code updated for record ID 1.");
            } else {
                $csrfError = "Failed to update sharing code.";
                logMessage("Error updating sharing code for record ID 1.");
            }
        } else {
            // Insert a new record
            $insertStmt = $pdo->prepare("INSERT INTO sharing_code (code, created_at, updated_at) VALUES (?, ?, ?)");
            $result = $insertStmt->execute([$code, date("Y-m-d H:i:s"), date("Y-m-d H:i:s")]);
            if ($result) {
                $message = "Sharing code saved successfully.";
                logMessage("New sharing code record inserted.");
            } else {
                $csrfError = "Failed to save sharing code.";
                logMessage("Error inserting new sharing code record.");
            }
        }
    }
}

// Generate a new CSRF token for the form.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve the current sharing code
$currentCode = '';
$stmt = $pdo->prepare("SELECT code FROM sharing_code WHERE id = 1");
$stmt->execute();
$record = $stmt->fetch(PDO::FETCH_ASSOC);
if ($record && isset($record['code'])) {
    $currentCode = $record['code'];
}
?>

<div class="container mt-5">
  <div class="row justify-content-center">
     <div class="col-md-8">
       <div class="card shadow">
         <div class="card-header bg-primary text-white">
           <h4 class="mb-0">Sharing Code</h4>
         </div>
         <div class="card-body">
           <?php if (!empty($csrfError)): ?>
             <div class="alert alert-danger"><?php echo htmlspecialchars($csrfError); ?></div>
           <?php elseif (!empty($message)): ?>
             <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
           <?php endif; ?>

           <form method="post" action="">
             <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
             <div class="form-group">
                 <label for="code">Enter Button Code to be Displayed on the User Site:</label>
                 <textarea class="form-control" id="code" name="code" rows="10" placeholder="Paste your HTML/JavaScript code here"><?php echo htmlspecialchars($currentCode); ?></textarea>
             </div>
             <button type="submit" class="btn btn-primary btn-block mt-3">Save Code</button>
           </form>
         </div>
       </div>
     </div>
  </div>
</div>

<?php include_once("../footer.php"); ?>
