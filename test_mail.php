<?php
require_once 'vendor/autoload.php';
require_once 'app/config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$envFile = '.env';
$env = parse_ini_file($envFile);

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = $env['MAIL_HOST'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = $env['MAIL_USER'] ?? '';
    $mail->Password   = $env['MAIL_PASS'] ?? '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
    $mail->Port       = 465;                                    // TCP port to connect to

    // Recipients
    $mail->setFrom($env['MAIL_USER'], 'EMS Test');
    $mail->addAddress($env['MAIL_USER']);                       // Send to self

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'SMTP Debug Test';
    $mail->Body    = 'If you see this, SMTP is working!';

    echo "<h3>Starting SMTP Test...</h3><pre>";
    $mail->send();
    echo "</pre><h3 style='color: green;'>Message has been sent!</h3>";
} catch (Exception $e) {
    echo "</pre><h3 style='color: red;'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</h3>";
}
