<?php
/**
 * ==========================================
 * EMAIL CONFIGURATION FILE
 * ==========================================
 * 
 * This file handles email functionality using PHPMailer library.
 * It provides a centralized function for sending emails throughout the application.
 * 
 * Features:
 * - SMTP configuration for Gmail
 * - Error handling and debugging
 * - Reusable sendMail function
 */

// Import PHPMailer classes for email functionality
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include Composer autoloader to load PHPMailer classes
require __DIR__ . '/../vendor/autoload.php';

/**
 * Send Email Function
 * 
 * @param string $to - Recipient email address
 * @param string $subject - Email subject line
 * @param string $body - Email body content
 * @param string $toName - Recipient name (optional)
 * @return bool - Returns true if email sent successfully, false otherwise
 */
function sendMail($to, $subject, $body, $toName = '') {
    // Create new PHPMailer instance with error handling enabled
    $mail = new PHPMailer(true);
    
    try {
        // ==========================================
        // SMTP SERVER CONFIGURATION
        // ==========================================
        
        // Enable SMTP for sending emails
        $mail->isSMTP();
        
        // Gmail SMTP server settings
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server address
        $mail->SMTPAuth = true;         // Enable SMTP authentication
        $mail->Username = 'bettfinlay@gmail.com'; // Your Gmail address
        $mail->Password = 'pmtsmfupbwvlntvr';     // Your Gmail app password (NOT regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
        $mail->Port = 587;              // Gmail SMTP port for TLS

        // ==========================================
        // EMAIL CONTENT CONFIGURATION
        // ==========================================
        
        // Set sender information
        $mail->setFrom('bettfinlay@gmail.com', 'Dentracare');
        
        // Add recipient
        $mail->addAddress($to, $toName);

        // Email format settings
        $mail->isHTML(false); // Set to true if you want HTML emails
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Send the email
        $mail->send();
        return true; // Email sent successfully
        
    } catch (Exception $e) {
        // Error handling - display error message for debugging
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        return false; // Email failed to send
    }
}