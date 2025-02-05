<?php
require_once("../../global.php");
include_once("../header.php");

// Role check (admins only)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3, 4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1, 3]);

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
    // Validate CSRF token.
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
        logMessage("CSRF token validation failed.");
    } else {
        // Process "Make Active" button (sets a record as active).
        if (isset($_POST['set_active'])) {
            $setActiveId = $_POST['set_active'];
            // First, update all records to inactive.
            $pdo->query("UPDATE sharing_code SET active = 0");
            // Then, mark the selected record as active.
            $stmt = $pdo->prepare("UPDATE sharing_code SET active = 1, updated_at = ? WHERE id = ?");
            if ($stmt->execute([date("Y-m-d H:i:s"), $setActiveId])) {
                $message = "Sharing code record (ID: $setActiveId) set as active.";
                logMessage("Sharing code record (ID: $setActiveId) set as active.");
            } else {
                $csrfError = "Failed to set sharing code as active.";
                logMessage("Error setting sharing code (ID: $setActiveId) as active.");
            }
        }
        // Process deletion.
        else if (isset($_POST['delete_record'])) {
            if ($isAdmin) {
                $deleteId = $_POST['delete_record'];
                $deleteStmt = $pdo->prepare("DELETE FROM sharing_code WHERE id = ?");
                $result = $deleteStmt->execute([$deleteId]);
                if ($result) {
                    $message = "Sharing code record deleted successfully.";
                    logMessage("Deleted sharing code record ID " . $deleteId);
                } else {
                    $csrfError = "Failed to delete sharing code.";
                    logMessage("Error deleting sharing code record ID " . $deleteId);
                }
            } else {
                $csrfError = "You are not authorized to delete records.";
                logMessage("Unauthorized deletion attempt by role " . $role);
            }
        }
        // Process add or update request.
        else {
            // Get the raw code input (preserving formatting).
            $code = $_POST['code'] ?? '';
            // Optionally get record id if we are updating an existing record.
            $recordId = $_POST['record_id'] ?? '';
            // Determine if this record should be active (checkbox).
            $active = isset($_POST['active']) ? 1 : 0;
            
            // If this record is to be active, unset the active flag on all others.
            if ($active == 1) {
                $pdo->query("UPDATE sharing_code SET active = 0");
            }
            
            if (!empty($recordId)) {
                // Update the existing record.
                $updateStmt = $pdo->prepare("UPDATE sharing_code SET code = ?, active = ?, updated_at = ? WHERE id = ?");
                $result = $updateStmt->execute([$code, $active, date("Y-m-d H:i:s"), $recordId]);
                if ($result) {
                    $message = "Sharing code updated successfully.";
                    logMessage("Sharing code updated for record ID " . $recordId);
                } else {
                    $csrfError = "Failed to update sharing code.";
                    logMessage("Error updating sharing code for record ID " . $recordId);
                }
            } else {
                // Insert a new record.
                $insertStmt = $pdo->prepare("INSERT INTO sharing_code (code, active, created_at, updated_at) VALUES (?, ?, ?, ?)");
                $result = $insertStmt->execute([$code, $active, date("Y-m-d H:i:s"), date("Y-m-d H:i:s")]);
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
}

// Generate a new CSRF token for the form.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve all sharing code records.
$stmt = $pdo->prepare("SELECT * FROM sharing_code ORDER BY created_at DESC");
$stmt->execute();
$sharingRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
  <div class="row">
    <div class="col">
      <h3>Existing Sharing Codes</h3>
      <?php if (!empty($sharingRecords) && is_array($sharingRecords)): ?>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Created At</th>
            <th>Code Snippet</th>
            <th>Active</th>
            <th>Action</th>
            <?php if ($isAdmin): ?>
            <th>Set Active</th>
            <th>Delete</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sharingRecords as $record): ?>
          <tr>
            <td><?php echo htmlspecialchars($record['id']); ?></td>
            <td><?php echo htmlspecialchars($record['created_at']); ?></td>
            <td><?php echo nl2br(htmlspecialchars(substr($record['code'], 0, 50))) . (strlen($record['code']) > 50 ? '...' : ''); ?></td>
            <td>
              <?php if ($record['active']): ?>
                <span class="badge bg-success">Active</span>
              <?php else: ?>
                <span class="badge bg-secondary">Inactive</span>
              <?php endif; ?>
            </td>
            <td>
              <!-- Edit button: when clicked, it populates the form below -->
              <button class="btn btn-sm btn-primary edit-btn" 
                      data-record-id="<?php echo $record['id']; ?>" 
                      data-code="<?php echo htmlspecialchars($record['code'], ENT_QUOTES); ?>" 
                      data-active="<?php echo $record['active']; ?>">
                Edit
              </button>
            </td>
            <?php if ($isAdmin): ?>
            <td>
              <?php if (!$record['active']): ?>
              <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" name="set_active" value="<?php echo $record['id']; ?>" class="btn btn-sm btn-warning">Make Active</button>
              </form>
              <?php else: ?>
              <span class="text-success">Active</span>
              <?php endif; ?>
            </td>
            <td>
              <!-- Delete button in a form for CSRF protection -->
              <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this record?');">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="delete_record" value="<?php echo $record['id']; ?>">
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
            <?php endif; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p>No sharing code records found.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Add/Update Sharing Code Form -->
<div class="container mt-5">
  <div class="row justify-content-center">
     <div class="col-md-8">
       <div class="card shadow">
         <div class="card-header bg-primary text-white">
           <h4 class="mb-0" id="form-header">Add / Edit Sharing Code</h4>
         </div>
         <div class="card-body">
           <?php if (!empty($csrfError)): ?>
             <div class="alert alert-danger" id="alert-message"><?php echo htmlspecialchars($csrfError); ?></div>
           <?php elseif (!empty($message)): ?>
             <div class="alert alert-success" id="alert-message"><?php echo htmlspecialchars($message); ?></div>
           <?php endif; ?>

           <form method="post" action="" id="sharing-form">
             <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
             <!-- Hidden field to store record ID for editing -->
             <input type="hidden" name="record_id" id="record_id" value="">
             <div class="form-group">
                 <label for="code">Enter Button Code to be Displayed on the User Site:</label>
                 <textarea class="form-control" id="code" name="code" rows="10" placeholder="Paste your HTML/JavaScript code here"><?php echo isset($currentCode) ? htmlspecialchars($currentCode) : ''; ?></textarea>
             </div>
             <div class="form-group form-check mt-3">
                <input type="checkbox" class="form-check-input" id="active" name="active" value="1">
                <label class="form-check-label" for="active">Set as Active Sharing Code</label>
             </div>
             <div class="form-group d-flex justify-content-between mt-3">
               <button type="submit" class="btn btn-primary">Save Code</button>
               <button type="button" class="btn btn-secondary" id="clear-form-btn">Clear Form</button>
             </div>
           </form>
         </div>
       </div>
     </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // When an edit button is clicked, populate the form with that record's code, record_id, and active status.
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const recordId = this.getAttribute('data-record-id');
            const code = this.getAttribute('data-code');
            const active = this.getAttribute('data-active');
            document.getElementById('record_id').value = recordId;
            document.getElementById('code').value = code;
            // Set the checkbox based on active status.
            document.getElementById('active').checked = (active === '1');
            // Update form header to indicate editing mode.
            document.getElementById('form-header').textContent = "Edit Sharing Code (ID: " + recordId + ")";
            // Scroll to the form.
            document.getElementById('code').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Clear Form button functionality.
    document.getElementById('clear-form-btn').addEventListener('click', function() {
        document.getElementById('record_id').value = '';
        document.getElementById('code').value = '';
        document.getElementById('active').checked = false;
        document.getElementById('form-header').textContent = "Add Sharing Code";
    });

    // Auto-hide alert messages after 5 seconds.
    const alertMessage = document.getElementById('alert-message');
    if (alertMessage) {
        setTimeout(() => {
            alertMessage.style.display = 'none';
        }, 5000);
    }
});
</script>

<?php include_once("../footer.php"); ?>
