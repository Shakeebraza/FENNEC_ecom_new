<?php
require_once("../../global.php");
include_once("../header.php");

// Only allow admins (roles 1 and 3)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

$csrfError = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
    } else {
        // Retrieve form fields
        $sendType = $_POST['send_type'] ?? 'single'; // 'single' or 'all'
        $recipient = trim($_POST['recipient'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body = $_POST['body'] ?? '';
        
        // Basic validation
        if ($subject === '' || $body === '') {
            $csrfError = "Subject and body are required.";
        } else {
            $headers = "From: admin@example.com\r\n";
            // If sending HTML, you may include content-type header:
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            if ($sendType === 'single') {
                // Validate the recipient email format
                if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    $csrfError = "Invalid email address.";
                } else {
                    if (mail($recipient, $subject, $body, $headers)) {
                        $message = "Email sent successfully to $recipient.";
                    } else {
                        $csrfError = "Failed to send email to $recipient.";
                    }
                }
            } elseif ($sendType === 'all') {
                // Send to all verified users (email_verified_at NOT NULL)
                $stmt = $pdo->prepare("SELECT email FROM users WHERE email_verified_at IS NOT NULL");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $failedCount = 0;
                $sentCount = 0;
                foreach ($users as $user) {
                    $userEmail = $user['email'];
                    // For each email, you may choose to replace placeholders if needed
                    if (mail($userEmail, $subject, $body, $headers)) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                    }
                }
                $message = "Email sent to {$sentCount} verified user(s)." . ($failedCount ? " Failed to send to {$failedCount} user(s)." : "");
            }
        }
    }
}

// Generate a new CSRF token.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-secondary text-white">
            <h3 class="mb-0">Send Email</h3>
            <p class="mb-0">Choose whether to send an email to a single user or to all verified users.</p>
        </div>
        <div class="card-body">
            <?php if (!empty($csrfError)): ?>
                <div class="alert alert-danger" id="alert-message"><?= htmlspecialchars($csrfError) ?></div>
            <?php elseif (!empty($message)): ?>
                <div class="alert alert-success" id="alert-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <!-- Choose sending mode -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Send Email To:</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="send_type" id="send_single" value="single" checked>
                        <label class="form-check-label" for="send_single">One User</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="send_type" id="send_all" value="all">
                        <label class="form-check-label" for="send_all">All Verified Users</label>
                    </div>
                </div>
                
                <!-- Recipient email (only shown when sending to one user) -->
                <div class="mb-3" id="recipientDiv">
                    <label for="recipient" class="form-label">Recipient Email:</label>
                    <input type="email" name="recipient" id="recipient" class="form-control" placeholder="user@example.com">
                </div>
                
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject:</label>
                    <input type="text" name="subject" id="subject" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="body" class="form-label">Message Body:</label>
                    <textarea name="body" id="body" class="form-control" rows="8" required></textarea>
                    <div class="form-text">You can use placeholders like {name} if desired.</div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Show or hide recipient field based on send_type selection
document.addEventListener("DOMContentLoaded", function() {
    const sendTypeRadios = document.querySelectorAll('input[name="send_type"]');
    const recipientDiv = document.getElementById('recipientDiv');
    
    function toggleRecipient() {
        const selected = document.querySelector('input[name="send_type"]:checked').value;
        if (selected === 'single') {
            recipientDiv.style.display = 'block';
        } else {
            recipientDiv.style.display = 'none';
        }
    }
    
    sendTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleRecipient);
    });
    
    toggleRecipient(); // Initialize on load

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
