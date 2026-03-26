<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{
    public static function sendOTP($email, $otp)
    {
        $mail = new PHPMailer(true);

        try {
            // Load environment variables directly from .env if needed, but they are already in $_ENV/$_SERVER or parsed in config.
            // Based on earlier view_file, database.php parses .env manually.
            // Let's parse it here too or assume they are available if we add it to index.php or something.
            // Actually let's just parse it for safety like database.php does.
            
            $envFile = dirname(dirname(__DIR__)) . '/.env';
            if (file_exists($envFile)) {
                $env = parse_ini_file($envFile);
            } else {
                throw new Exception("Environment file not found.");
            }

            // Server settings
            $mail->isSMTP();
            $mail->Host       = $env['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $env['MAIL_USER'] ?? '';
            $mail->Password   = $env['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom($env['MAIL_USER'], 'Event Management System');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                    <h2 style='color: #2d5a27; text-align: center;'>Verify Your Identity</h2>
                    <p>Hello,</p>
                    <p>Your 6-digit verification code is:</p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <span style='font-size: 32px; font-weight: bold; letter-spacing: 5px; background: #f4f4f4; padding: 10px 20px; border-radius: 5px;'>$otp</span>
                    </div>
                    <p>This code will expire in 10 minutes.</p>
                    <p>If you did not request this code, please ignore this email.</p>
                    <hr style='border: 0; border-top: 1px solid #eee;'>
                    <p style='font-size: 12px; color: #777; text-align: center;'>&copy; " . date('Y') . " Event Management System</p>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
