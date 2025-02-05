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

$csrfError = '';
$message = '';

// Define upload directory for icon schemes (adjust path as needed)
$uploadDir = __DIR__ . '/../../uploads/icon_schemes/';
$uploadUrl = $urlval . "uploads/icon_schemes/"; // URL base for uploaded files

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
    } else {
        // Process "Make Active" button request
        if (isset($_POST['set_active'])) {
            $schemeId = intval($_POST['set_active']);
            // Update all schemes to inactive first
            $pdo->query("UPDATE icon_schemes SET active = 0");
            $stmt = $pdo->prepare("UPDATE icon_schemes SET active = 1, updated_at = ? WHERE id = ?");
            if ($stmt->execute([date("Y-m-d H:i:s"), $schemeId])) {
                $message = "Icon scheme has been set as active.";
            } else {
                $csrfError = "Failed to set scheme as active.";
            }
        }
        // Process deletion
        else if (isset($_POST['delete_scheme'])) {
            if ($isAdmin) {
                $schemeId = intval($_POST['delete_scheme']);
                // Optionally, delete physical files from server
                $stmt = $pdo->prepare("SELECT header_icon, footer_icon, sidebar_icon, mobile_icon, contact_seller, sellers_store, add_to_favorites, buy_now, classified_details, print_classified FROM icon_schemes WHERE id = ?");
                $stmt->execute([$schemeId]);
                $scheme = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($scheme) {
                    foreach (['header_icon', 'footer_icon', 'sidebar_icon', 'mobile_icon', 'contact_seller', 'sellers_store', 'add_to_favorites', 'buy_now', 'classified_details', 'print_classified'] as $field) {
                        if (!empty($scheme[$field]) && file_exists(__DIR__ . '/../../' . $scheme[$field])) {
                            unlink(__DIR__ . '/../../' . $scheme[$field]);
                        }
                    }
                }
                // Delete record from DB
                $stmt = $pdo->prepare("DELETE FROM icon_schemes WHERE id = ?");
                if ($stmt->execute([$schemeId])) {
                    $message = "Icon scheme deleted successfully.";
                } else {
                    $csrfError = "Failed to delete icon scheme.";
                }
            } else {
                $csrfError = "You are not authorized to delete icon schemes.";
            }
        }
        // Process Add/Edit scheme
        else {
            $schemeName = $_POST['scheme_name'] ?? '';
            $schemeId = $_POST['scheme_id'] ?? '';
            // Capture active checkbox (1 if checked, 0 if not)
            $active = isset($_POST['active']) ? 1 : 0;
            // If admin set active via form, unselect all other schemes
            if ($active == 1) {
                $pdo->query("UPDATE icon_schemes SET active = 0");
            }
            // If updating, retrieve the existing record for file retention
            $existing = null;
            if (!empty($schemeId)) {
                $stmt = $pdo->prepare("SELECT * FROM icon_schemes WHERE id = ?");
                $stmt->execute([$schemeId]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            // Define the fields to be processed (all file fields)
            $fields = [
                'header_icon', 'footer_icon', 'sidebar_icon', 'mobile_icon',
                'contact_seller', 'sellers_store', 'add_to_favorites', 'buy_now',
                'classified_details', 'print_classified'
            ];
            $iconPaths = [];
            foreach ($fields as $field) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES[$field]['tmp_name'];
                    $filename = time() . "_" . basename($_FILES[$field]['name']);
                    $targetPath = $uploadDir . $filename;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        // Save relative path to DB (adjust if needed)
                        $iconPaths[$field] = "uploads/icon_schemes/" . $filename;
                    }
                } else {
                    // On update, if no new file is provided, retain the existing value
                    if ($existing && isset($existing[$field])) {
                        $iconPaths[$field] = $existing[$field];
                    } else {
                        $iconPaths[$field] = null;
                    }
                }
            }
            
            if (!empty($schemeId)) {
                // Update existing scheme
                $stmt = $pdo->prepare("UPDATE icon_schemes SET scheme_name = ?, header_icon = ?, footer_icon = ?, sidebar_icon = ?, mobile_icon = ?, contact_seller = ?, sellers_store = ?, add_to_favorites = ?, buy_now = ?, classified_details = ?, print_classified = ?, active = ?, updated_at = ? WHERE id = ?");
                $result = $stmt->execute([
                    $schemeName,
                    $iconPaths['header_icon'],
                    $iconPaths['footer_icon'],
                    $iconPaths['sidebar_icon'],
                    $iconPaths['mobile_icon'],
                    $iconPaths['contact_seller'],
                    $iconPaths['sellers_store'],
                    $iconPaths['add_to_favorites'],
                    $iconPaths['buy_now'],
                    $iconPaths['classified_details'],
                    $iconPaths['print_classified'],
                    $active,
                    date("Y-m-d H:i:s"),
                    $schemeId
                ]);
                if ($result) {
                    $message = "Icon scheme updated successfully.";
                } else {
                    $csrfError = "Failed to update icon scheme.";
                }
            } else {
                // Insert new scheme
                $stmt = $pdo->prepare("INSERT INTO icon_schemes (scheme_name, header_icon, footer_icon, sidebar_icon, mobile_icon, contact_seller, sellers_store, add_to_favorites, buy_now, classified_details, print_classified, active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([
                    $schemeName,
                    $iconPaths['header_icon'],
                    $iconPaths['footer_icon'],
                    $iconPaths['sidebar_icon'],
                    $iconPaths['mobile_icon'],
                    $iconPaths['contact_seller'],
                    $iconPaths['sellers_store'],
                    $iconPaths['add_to_favorites'],
                    $iconPaths['buy_now'],
                    $iconPaths['classified_details'],
                    $iconPaths['print_classified'],
                    $active,
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s")
                ]);
                if ($result) {
                    $message = "Icon scheme added successfully.";
                } else {
                    $csrfError = "Failed to add icon scheme.";
                }
            }
        }
    }
}

// Generate a new CSRF token for the form.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve all icon schemes
$stmt = $pdo->prepare("SELECT * FROM icon_schemes ORDER BY created_at DESC");
$stmt->execute();
$iconSchemes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
  <h3>Icon Schemes</h3>
  <?php if (!empty($csrfError)): ?>
    <div class="alert alert-danger" id="alert-message"><?php echo htmlspecialchars($csrfError); ?></div>
  <?php elseif (!empty($message)): ?>
    <div class="alert alert-success" id="alert-message"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  
  <!-- List Existing Icon Schemes -->
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Scheme Name</th>
        <th>Header Icon</th>
        <th>Footer Icon</th>
        <th>Sidebar Icon</th>
        <th>Mobile Icon</th>
        <th>Contact Seller</th>
        <th>Seller's Store</th>
        <th>Add to Favorites</th>
        <th>Buy Now</th>
        <th>Classified Details</th>
        <th>Print Classified</th>
        <th>Active</th>
        <th>Action</th>
        <th>Set Active</th>
        <?php if ($isAdmin): ?>
          <th>Delete</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($iconSchemes)): ?>
      <?php foreach ($iconSchemes as $scheme): ?>
      <tr>
        <td><?php echo htmlspecialchars($scheme['id']); ?></td>
        <td><?php echo htmlspecialchars($scheme['scheme_name']); ?></td>
        <td>
          <?php if (!empty($scheme['header_icon'])): ?>
            <img src="<?php echo $urlval . $scheme['header_icon']; ?>" alt="Header Icon" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['footer_icon'])): ?>
            <img src="<?php echo $urlval . $scheme['footer_icon']; ?>" alt="Footer Icon" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['sidebar_icon'])): ?>
            <img src="<?php echo $urlval . $scheme['sidebar_icon']; ?>" alt="Sidebar Icon" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['mobile_icon'])): ?>
            <img src="<?php echo $urlval . $scheme['mobile_icon']; ?>" alt="Mobile Icon" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['contact_seller'])): ?>
            <img src="<?php echo $urlval . $scheme['contact_seller']; ?>" alt="Contact Seller" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['sellers_store'])): ?>
            <img src="<?php echo $urlval . $scheme['sellers_store']; ?>" alt="Seller's Store" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['add_to_favorites'])): ?>
            <img src="<?php echo $urlval . $scheme['add_to_favorites']; ?>" alt="Add to Favorites" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['buy_now'])): ?>
            <img src="<?php echo $urlval . $scheme['buy_now']; ?>" alt="Buy Now" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['classified_details'])): ?>
            <img src="<?php echo $urlval . $scheme['classified_details']; ?>" alt="Classified Details" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($scheme['print_classified'])): ?>
            <img src="<?php echo $urlval . $scheme['print_classified']; ?>" alt="Print Classified" width="50">
          <?php endif; ?>
        </td>
        <td>
          <?php echo $scheme['active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?>
        </td>
        <td>
          <!-- Edit button includes the active state -->
          <button class="btn btn-sm btn-primary edit-btn" 
                  data-scheme-id="<?php echo $scheme['id']; ?>" 
                  data-scheme-name="<?php echo htmlspecialchars($scheme['scheme_name'], ENT_QUOTES); ?>" 
                  data-active="<?php echo $scheme['active']; ?>">
            Edit
          </button>
        </td>
        <td>
          <?php if (!$scheme['active']): ?>
            <form method="post" action="">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <button type="submit" name="set_active" value="<?php echo $scheme['id']; ?>" class="btn btn-sm btn-warning">Make Active</button>
            </form>
          <?php else: ?>
            <span class="text-success">Active</span>
          <?php endif; ?>
        </td>
        <?php if ($isAdmin): ?>
        <td>
          <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this scheme?');">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="delete_scheme" value="<?php echo $scheme['id']; ?>">
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
          </form>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr>
        <td colspan="17">No icon schemes found.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Add / Edit Icon Scheme Form -->
  <div class="card mt-4">
    <div class="card-header bg-primary text-white">
      <h4 id="form-header">Add / Edit Icon Scheme</h4>
    </div>
    <div class="card-body">
      <form method="post" action="" enctype="multipart/form-data" id="scheme-form">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <!-- Hidden field for scheme id -->
        <input type="hidden" name="scheme_id" id="scheme_id" value="">
        <div class="mb-3">
          <label for="scheme_name" class="form-label">Scheme Name</label>
          <input type="text" class="form-control" id="scheme_name" name="scheme_name" placeholder="Enter scheme name" required>
        </div>
        <!-- File inputs for each icon field -->
        <div class="mb-3">
          <label for="header_icon" class="form-label">Header Icon</label>
          <input type="file" class="form-control" id="header_icon" name="header_icon" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="footer_icon" class="form-label">Footer Icon</label>
          <input type="file" class="form-control" id="footer_icon" name="footer_icon" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="sidebar_icon" class="form-label">Sidebar Icon</label>
          <input type="file" class="form-control" id="sidebar_icon" name="sidebar_icon" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="mobile_icon" class="form-label">Mobile Icon</label>
          <input type="file" class="form-control" id="mobile_icon" name="mobile_icon" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="contact_seller" class="form-label">Contact Seller</label>
          <input type="file" class="form-control" id="contact_seller" name="contact_seller" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="sellers_store" class="form-label">Seller's Store</label>
          <input type="file" class="form-control" id="sellers_store" name="sellers_store" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="add_to_favorites" class="form-label">Add to Favorites</label>
          <input type="file" class="form-control" id="add_to_favorites" name="add_to_favorites" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="buy_now" class="form-label">Buy Now</label>
          <input type="file" class="form-control" id="buy_now" name="buy_now" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="classified_details" class="form-label">Classified Details</label>
          <input type="file" class="form-control" id="classified_details" name="classified_details" accept="image/*">
        </div>
        <div class="mb-3">
          <label for="print_classified" class="form-label">Print Classified</label>
          <input type="file" class="form-control" id="print_classified" name="print_classified" accept="image/*">
        </div>
        <!-- Checkbox for Active Scheme -->
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="active" name="active" value="1">
          <label class="form-check-label" for="active">Set as Active Scheme</label>
        </div>
        <div class="d-flex justify-content-between">
          <button type="submit" class="btn btn-primary">Save Scheme</button>
          <button type="button" class="btn btn-secondary" id="clear-form-btn">Clear Form</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Edit button functionality for icon schemes
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const schemeId = this.getAttribute('data-scheme-id');
            const schemeName = this.getAttribute('data-scheme-name');
            const active = this.getAttribute('data-active');
            document.getElementById('scheme_id').value = schemeId;
            document.getElementById('scheme_name').value = schemeName;
            document.getElementById('form-header').textContent = "Edit Icon Scheme (ID: " + schemeId + ")";
            // Set the active checkbox based on the record
            document.getElementById('active').checked = (active === '1');
            // Scroll to form
            document.getElementById('scheme-form').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Clear form button functionality
    document.getElementById('clear-form-btn').addEventListener('click', function() {
        document.getElementById('scheme_id').value = '';
        document.getElementById('scheme-form').reset();
        document.getElementById('form-header').textContent = "Add Icon Scheme";
    });

    // Auto-hide alert messages after 5 seconds
    const alertMessage = document.getElementById('alert-message');
    if (alertMessage) {
        setTimeout(() => {
            alertMessage.style.display = 'none';
        }, 5000);
    }
});
</script>

<?php include_once("../footer.php"); ?>
