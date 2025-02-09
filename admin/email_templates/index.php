<?php
require_once("../../global.php");
include_once("../header.php");

// Role check: allow only admins (roles 1 and 3)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

$csrfError = '';
$message = '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $csrfError = "Invalid CSRF token!";
        } else {
            if ($_POST['action'] === 'save_template') {
                // Save (add or update) an email template
                $id = $_POST['template_id'] ?? '';
                $template_key = trim($_POST['template_key'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $subject = trim($_POST['subject'] ?? '');
                $body = $_POST['body'] ?? '';
                $format = $_POST['format'] ?? 'html';
                $enabled = isset($_POST['enabled']) ? 1 : 0;
                
                if ($id) {
                    // Update template
                    $stmt = $pdo->prepare("UPDATE email_templates SET template_key=?, title=?, subject=?, body=?, format=?, enabled=?, updated_at=? WHERE id=?");
                    $result = $stmt->execute([
                        $template_key, $title, $subject, $body, $format, $enabled, date("Y-m-d H:i:s"), $id
                    ]);
                    if ($result) {
                        $message = "Email template updated successfully.";
                    } else {
                        $csrfError = "Failed to update template.";
                    }
                } else {
                    // Insert new template
                    $stmt = $pdo->prepare("INSERT INTO email_templates (template_key, title, subject, body, format, enabled, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $result = $stmt->execute([
                        $template_key, $title, $subject, $body, $format, $enabled, date("Y-m-d H:i:s"), date("Y-m-d H:i:s")
                    ]);
                    if ($result) {
                        $message = "Email template added successfully.";
                    } else {
                        $csrfError = "Failed to add template.";
                    }
                }
            } else if ($_POST['action'] === 'delete_template') {
                // Delete an email template
                $deleteId = $_POST['template_id'] ?? '';
                if ($deleteId) {
                    $stmt = $pdo->prepare("DELETE FROM email_templates WHERE id=?");
                    if ($stmt->execute([$deleteId])) {
                        $message = "Email template deleted successfully.";
                    } else {
                        $csrfError = "Failed to delete template.";
                    }
                }
            } else if ($_POST['action'] === 'toggle_enabled') {
                // Toggle enabled state for a template
                $toggleId = $_POST['template_id'] ?? '';
                if ($toggleId) {
                    // Retrieve current state
                    $stmt = $pdo->prepare("SELECT enabled FROM email_templates WHERE id=?");
                    $stmt->execute([$toggleId]);
                    $tpl = $stmt->fetch(PDO::FETCH_ASSOC);
                    $newState = ($tpl['enabled'] ? 0 : 1);
                    $stmt = $pdo->prepare("UPDATE email_templates SET enabled=?, updated_at=? WHERE id=?");
                    if ($stmt->execute([$newState, date("Y-m-d H:i:s"), $toggleId])) {
                        $message = "Template enabled state toggled successfully.";
                    } else {
                        $csrfError = "Failed to toggle enabled state.";
                    }
                }
            } else if ($_POST['action'] === 'send_test_email') {
                // Send test email using selected template
                $test_template_id = $_POST['test_template_id'] ?? '';
                $recipient = $_POST['recipient'] ?? '';
                if ($test_template_id && $recipient) {
                    $stmt = $pdo->prepare("SELECT subject, body, format, enabled FROM email_templates WHERE id=?");
                    $stmt->execute([$test_template_id]);
                    $template = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($template && $template['enabled']) {
                        $subject = $template['subject'];
                        $body = $template['body'];
                        // Set headers based on email format
                        $headers = "From: admin@example.com\r\n";
                        if ($template['format'] === 'html') {
                            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        } else {
                            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                        }
                        if (smtp_mailer($recipient, $subject, $body, $headers)) {
                            $message = "Test email sent successfully to $recipient.";
                        } else {
                            $csrfError = "Failed to send test email.";
                        }
                    } else {
                        $csrfError = "Template not found or disabled.";
                    }
                } else {
                    $csrfError = "Please select a template and provide a recipient email.";
                }
            }
        }
    }
}

// Generate a new CSRF token.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve all email templates.
$stmt = $pdo->prepare("SELECT * FROM email_templates ORDER BY title ASC");
$stmt->execute();
$emailTemplates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h3 class="mb-0">Email Templates Management</h3>
            <p class="mb-0">Manage and test the email templates sent to users.</p>
        </div>
        <div class="card-body">
            <?php if (!empty($csrfError)): ?>
                <div class="alert alert-danger" id="alert-message"><?= htmlspecialchars($csrfError) ?></div>
            <?php elseif (!empty($message)): ?>
                <div class="alert alert-success" id="alert-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <!-- Template List -->
            <div class="mb-4">
                <h4>Existing Templates</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Key</th>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Format</th>
                                <th>Enabled</th>
                                <th style="width: 25%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($emailTemplates): ?>
                                <?php foreach ($emailTemplates as $tpl): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($tpl['id']) ?></td>
                                        <td><?= htmlspecialchars($tpl['template_key']) ?></td>
                                        <td><?= htmlspecialchars($tpl['title']) ?></td>
                                        <td><?= htmlspecialchars($tpl['subject']) ?></td>
                                        <td><?= htmlspecialchars(strtoupper($tpl['format'])) ?></td>
                                        <td><?= $tpl['enabled'] ? 'Yes' : 'No' ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-btn mb-1"
                                                data-id="<?= $tpl['id'] ?>"
                                                data-key="<?= htmlspecialchars($tpl['template_key'], ENT_QUOTES) ?>"
                                                data-title="<?= htmlspecialchars($tpl['title'], ENT_QUOTES) ?>"
                                                data-subject="<?= htmlspecialchars($tpl['subject'], ENT_QUOTES) ?>"
                                                data-body="<?= htmlspecialchars($tpl['body'], ENT_QUOTES) ?>"
                                                data-format="<?= $tpl['format'] ?>"
                                                data-enabled="<?= $tpl['enabled'] ?>">
                                                Edit
                                            </button>
                                            <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this template?');">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <input type="hidden" name="template_id" value="<?= $tpl['id'] ?>">
                                                <input type="hidden" name="action" value="delete_template">
                                                <button type="submit" class="btn btn-sm btn-danger mb-1">Delete</button>
                                            </form>
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <input type="hidden" name="template_id" value="<?= $tpl['id'] ?>">
                                                <input type="hidden" name="action" value="toggle_enabled">
                                                <button type="submit" class="btn btn-sm btn-warning mb-1">
                                                    <?= $tpl['enabled'] ? 'Disable' : 'Enable' ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">No templates found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add/Edit Template Form -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h4 id="form-header" class="mb-0">Add New Email Template</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="" id="template-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="template_id" id="template_id" value="">
                        <div class="form-group mb-3">
                            <label class="form-label">Template Key:</label>
                            <input type="text" name="template_key" id="template_key" class="form-control" placeholder="Unique key identifier" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Title:</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Template title" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Subject:</label>
                            <input type="text" name="subject" id="subject" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Body:</label>
                            <textarea name="body" id="body" class="form-control" rows="6" required></textarea>
                            <small class="form-text text-muted">
                                Use placeholders such as {name}, {verification_link}, {reset_link}, etc.
                            </small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Email Format:</label>
                            <select name="format" id="format" class="form-control">
                                <option value="html">HTML</option>
                                <option value="text">Plain Text</option>
                            </select>
                        </div>
                        <div class="form-group form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="enabled" name="enabled" value="1">
                            <label class="form-check-label" for="enabled">Enabled (Send this email to users)</label>
                        </div>
                        <input type="hidden" name="action" value="save_template">
                        <button type="submit" class="btn btn-primary">Save Template</button>
                        <button type="button" class="btn btn-secondary" id="clear-form-btn">Clear Form</button>
                    </form>
                </div>
            </div>

            <!-- Test Email Sending Section -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Send Test Email</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="send_test_email">
                        <div class="form-group mb-3">
                            <label for="test_template_id" class="form-label">Select Email Template:</label>
                            <select name="test_template_id" id="test_template_id" class="form-control" required>
                                <option value="">Select an email template</option>
                                <option value="">--------------------------------</option>
                                <?php foreach ($emailTemplates as $tpl): ?>
                                    <option value="<?= $tpl['id'] ?>">
                                        <?= htmlspecialchars($tpl['title']) ?> (ID: <?= $tpl['id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="recipient" class="form-label">Recipient Email:</label>
                            <input type="email" name="recipient" id="recipient" class="form-control" placeholder="test@example.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Test Email</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    // Auto-hide alert messages after 5 seconds.
    const alertMessage = document.getElementById('alert-message');
    if (alertMessage) {
        setTimeout(() => {
            alertMessage.style.display = 'none';
        }, 5000);
    }
    
    // Populate the Add/Edit form when clicking an edit button.
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(function(btn){
        btn.addEventListener('click', function(){
            document.getElementById('template_id').value = this.getAttribute('data-id');
            document.getElementById('template_key').value = this.getAttribute('data-key');
            document.getElementById('title').value = this.getAttribute('data-title');
            document.getElementById('subject').value = this.getAttribute('data-subject');
            document.getElementById('body').value = this.getAttribute('data-body');
            document.getElementById('format').value = this.getAttribute('data-format');
            const enabled = this.getAttribute('data-enabled');
            document.getElementById('enabled').checked = (enabled === '1');
            document.getElementById('form-header').textContent = "Edit Email Template (ID: " + this.getAttribute('data-id') + ")";
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    
    // Clear form button functionality.
    document.getElementById('clear-form-btn').addEventListener('click', function(){
        document.getElementById('template_id').value = '';
        document.getElementById('template_key').value = '';
        document.getElementById('title').value = '';
        document.getElementById('subject').value = '';
        document.getElementById('body').value = '';
        document.getElementById('format').value = 'html';
        document.getElementById('enabled').checked = false;
        document.getElementById('form-header').textContent = "Add New Email Template";
    });
});
</script>

<?php include_once("../footer.php"); ?>
