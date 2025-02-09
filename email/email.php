<?php
include('smtp/PHPMailerAutoload.php');

/**
 * Writes a log message to a file.
 *
 * @param string $message The message to log.
 */
function writeLog($message) {
    $logFile = 'email.log'; // Log file name or path
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] " . $message . PHP_EOL, FILE_APPEND);
}

function smtp_mailer($to, $subject, $msg, $attachments = []) {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host       = "smtp.gmail.com";
    $mail->Port       = 465;
    $mail->IsHTML(true);
    $mail->CharSet    = 'UTF-8';
    // $mail->SMTPDebug = 3;

    $mail->Username   = "man411210@gmail.com";
    $mail->Password   = "dsahhfifikerwgzz";
    $mail->SetFrom("man411210@gmail.com");
    $mail->Subject    = $subject;
    $mail->Body       = $msg;

    // If $to is a string and contains a comma, split it into an array
    if (!is_array($to) && strpos($to, ',') !== false) {
        $to = array_map('trim', explode(',', $to));
    }

    // Logging and adding recipients
    if (is_array($to)) {
        $recipients = implode(', ', $to);
        writeLog("Preparing to send email to multiple recipients: " . $recipients);
        foreach ($to as $recipient) {
            if (!empty($recipient)) {
                $mail->addAddress($recipient);
            }
        }
    } else {
        writeLog("Preparing to send email to recipient: " . $to);
        $mail->addAddress($to);
    }

    // Logging attachments, if any
    if (is_array($attachments)) {
        foreach ($attachments as $filePath) {
            if (file_exists($filePath)) {
                writeLog("Attaching file: " . $filePath);
                $mail->addAttachment($filePath);
            } else {
                writeLog("Attachment missing: " . $filePath);
            }
        }
    }

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true 
        ]
    ];

    // Log the sending attempt
    writeLog("Attempting to send email with subject: " . $subject);
    
    if (!$mail->Send()) {
        writeLog("Mailer Error: " . $mail->ErrorInfo);
        return $mail->ErrorInfo;
    } else {
        writeLog("Email successfully sent with subject: " . $subject . " to: " . (is_array($to) ? implode(', ', $to) : $to));
        return 'sent';
    }
}
?>
