<?php
require 'config/mail.php';

// Replace with your real email address for testing
$to = 'bettfinlay@gmail.com';
$subject = 'Test Email from Dentracare';
$body = 'This is a test email from Dentracare.';
$toName = 'Your Name';

if (sendMail($to, $subject, $body, $toName)) {
    echo 'Sent!';
} else {
    echo 'Failed to send email.';
} 