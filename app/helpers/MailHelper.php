<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{
    public static function sendOTP($email, $otp)
    {
        $mail = new PHPMailer(true);

        try {
            $envFile = dirname(dirname(__DIR__)) . '/.env';
            if (file_exists($envFile)) {
                $env = parse_ini_file($envFile);
            } else {
                throw new Exception("Environment file not found.");
            }

            $mail->isSMTP();
            $mail->Host       = $env['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $env['MAIL_USER'] ?? '';
            $mail->Password   = $env['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($env['MAIL_USER'], 'Event Management System');
            $mail->addAddress($email);

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

    public static function sendTicket($email, $booking, $tickets, $transactionId = null)
    {
        $mail = new PHPMailer(true);
        try {
            $envFile = dirname(dirname(__DIR__)) . '/.env';
            if (file_exists($envFile)) {
                $env = parse_ini_file($envFile);
            } else {
                throw new Exception("Environment file not found.");
            }

            $mail->isSMTP();
            $mail->Host       = $env['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $env['MAIL_USER'] ?? '';
            $mail->Password   = $env['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($env['MAIL_USER'], 'Event Management System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Ticket Booked! - ' . ($booking['event_title'] ?? 'Your Concert Ticket');
            
            $ticketHtml = '';
            foreach($tickets as $t) {
                $ticketHtml .= "<div style='background: #f8fafc; padding: 10px; border-radius: 5px; margin-bottom: 5px; border-left: 4px solid #246A55;'>
                                    <strong>Ticket Code:</strong> <span style='color: #246A55; font-weight: bold;'>{$t['ticket_code']}</span>
                                </div>";
            }

            $txHtml = $transactionId ? "<p><strong>TX ID:</strong> <span style='font-family: monospace; color: #64748b;'>$transactionId</span></p>" : "";

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <h1 style='color: #246A55; margin-bottom: 5px;'>Ticket Booked!</h1>
                        <p style='color: #64748b;'>Your concert ticket has been reserved and paid successfully.</p>
                    </div>
                    
                    <div style='background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>
                        <h3 style='margin-top: 0; color: #1e293b;'>Booking Details</h3>
                        <p><strong>Event:</strong> " . ($booking['event_title'] ?? 'Concert') . "</p>
                        <p><strong>Date:</strong> " . date('M d, Y', strtotime($booking['event_date'])) . "</p>
                        <p><strong>Status:</strong> Paid & Confirmed</p>
                        <p><strong>Amount Paid:</strong> NPR " . number_format($booking['total_amount'], 2) . "</p>
                        $txHtml
                    </div>

                    <h3 style='color: #1e293b;'>Your Tickets</h3>
                    $ticketHtml
                    
                    <p style='margin-top: 20px;'>Please print your ticket or show the code at the entrance for entry.</p>
                    <p>Your confirmation details are also available in your dashboard.</p>
                    
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #777; text-align: center;'>&copy; " . date('Y') . " Event Management System</p>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Ticket Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
