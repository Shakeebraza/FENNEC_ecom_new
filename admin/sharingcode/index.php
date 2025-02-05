<?php
require_once("../../global.php");
include_once("../header.php");

// Role check (admins only)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1,3]);

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
        // Get the raw code input (do not trim, to preserve formatting)
        $code = $_POST['code'] ?? '';
        // Optionally get record id if we are updating an existing record
        $recordId = $_POST['record_id'] ?? '';

        if (!empty($recordId)) {
            // Update the existing record with the given record ID
            $updateStmt = $pdo->prepare("UPDATE sharing_code SET code = ?, updated_at = ? WHERE id = ?");
            $result = $updateStmt->execute([$code, date("Y-m-d H:i:s"), $recordId]);
            if ($result) {
                $message = "Sharing code updated successfully.";
                logMessage("Sharing code updated for record ID " . $recordId);
            } else {
                $csrfError = "Failed to update sharing code.";
                logMessage("Error updating sharing code for record ID " . $recordId);
            }
        } else {
            // Insert a new record.
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

// Retrieve all sharing code records
$sharingRecords = $dbFunctions->getDatanotenc("sharing_code", "1"); // "1" as condition returns all rows

// Retrieve a list of dummy code snippets
$dummyCodes = [
    [
        'title' => 'AddThis Button',
        'code'  => '<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style">
    <a href="http://www.addthis.com/bookmark.php?v=250&username=xa-4c93064b1efbb8be" class="addthis_button_compact">Share</a>
    <span class="addthis_separator">|</span>
    <a class="addthis_button_preferred_1"></a>
    <a class="addthis_button_preferred_2"></a>
    <a class="addthis_button_preferred_3"></a>
    <a class="addthis_button_preferred_4"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c93064b1efbb8be"></script>
<!-- AddThis Button END -->'
    ],
    [
        'title' => 'Simple Share Button',
        'code'  => '<button class="share-btn">Share Now</button>'
    ]
];
?>

<!-- Display Existing Sharing Codes -->
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
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sharingRecords as $record): ?>
          <tr>
            <td><?php echo htmlspecialchars($record['id']); ?></td>
            <td><?php echo htmlspecialchars($record['created_at']); ?></td>
            <td><?php echo nl2br(htmlspecialchars(substr($record['code'], 0, 50))) . (strlen($record['code']) > 50 ? '...' : ''); ?></td>
            <td>
              <!-- Edit button: when clicked, it populates the form below -->
              <button class="btn btn-sm btn-primary edit-btn" data-record-id="<?php echo $record['id']; ?>" data-code="<?php echo htmlspecialchars($record['code'], ENT_QUOTES); ?>">Edit</button>
            </td>
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
           <h4 class="mb-0">Add / Edit Sharing Code</h4>
         </div>
         <div class="card-body">
           <?php if (!empty($csrfError)): ?>
             <div class="alert alert-danger"><?php echo htmlspecialchars($csrfError); ?></div>
           <?php elseif (!empty($message)): ?>
             <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
           <?php endif; ?>

           <form method="post" action="">
             <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
             <!-- Hidden field to store record ID for editing -->
             <input type="hidden" name="record_id" id="record_id" value="">
             <div class="form-group">
                 <label for="code">Enter Button Code to be Displayed on the User Site:</label>
                 <textarea class="form-control" id="code" name="code" rows="10" placeholder="Paste your HTML/JavaScript code here"><?php echo isset($currentCode) ? htmlspecialchars($currentCode) : ''; ?></textarea>
             </div>
             <button type="submit" class="btn btn-primary btn-block mt-3">Save Code</button>
           </form>
         </div>
       </div>
     </div>
  </div>
</div>

<!-- Dummy Codes Section -->
<div class="container mt-5">
  <div class="row">
    <div class="col">
      <h3>Dummy Codes</h3>
      <div class="list-group">
        <?php foreach ($dummyCodes as $dummy): ?>
          <button type="button" class="list-group-item list-group-item-action dummy-code-btn" data-code="<?php echo htmlspecialchars($dummy['code'], ENT_QUOTES); ?>">
            <?php echo htmlspecialchars($dummy['title']); ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // When an edit button is clicked, populate the form with that record's code and set the record_id
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const recordId = this.getAttribute('data-record-id');
            const code = this.getAttribute('data-code');
            document.getElementById('record_id').value = recordId;
            document.getElementById('code').value = code;
            // Optionally, scroll to the form:
            document.getElementById('code').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // When a dummy code button is clicked, populate the form with that dummy code
    const dummyButtons = document.querySelectorAll('.dummy-code-btn');
    dummyButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            document.getElementById('code').value = code;
            // Clear any record_id to treat as a new entry if desired.
            document.getElementById('record_id').value = '';
            // Optionally, scroll to the form:
            document.getElementById('code').scrollIntoView({ behavior: 'smooth' });
        });
    });
});
</script>

<?php include_once("../footer.php"); ?>
