<?php
if (!isset($booking_id) || !isset($booking)) {
    header('Location: /EventManagementSystem/public/client/bookings');
    exit;
}

$eventTitle = htmlspecialchars($booking['event_title'] ?? 'Your booking');
$reasonCode = strtolower((string) ($reason ?? 'stripe_error'));

$title = 'Payment Failed';
$message = 'We could not complete your payment. Please try again from your booking page.';

if ($reasonCode === 'verification_failed') {
    $title = 'Payment Verification Failed';
    $message = 'Your payment could not be verified right now. Please open your booking and pay again.';
} elseif ($reasonCode === 'amount_limit') {
    $title = 'Amount Exceeds Stripe Limit';
    $maxAmount = isset($_GET['max']) ? (float) $_GET['max'] : 999999.99;
    $currentAmount = isset($_GET['amount']) ? (float) $_GET['amount'] : ((float) ($booking['total_amount'] ?? 0) * 0.50);
    $message = 'Stripe Checkout allows up to NPR ' . number_format($maxAmount, 2) . ' per payment, but this advance is NPR ' . number_format($currentAmount, 2) . '. Please use a lower package amount.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - e-Plan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --danger: #dc2626;
            --danger-soft: #fee2e2;
            --primary: #1f6f59;
            --primary-dark: #145747;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at top left, #ecfeff 0%, var(--bg) 45%);
            font-family: 'Inter', sans-serif;
            color: var(--text);
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 520px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .top {
            padding: 24px;
            background: var(--danger-soft);
            border-bottom: 1px solid #fecaca;
        }

        .icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #fff;
            color: var(--danger);
            font-size: 28px;
            margin-bottom: 14px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 24px;
            color: #991b1b;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.5;
            font-size: 14px;
        }

        .content {
            padding: 24px;
        }

        .booking-note {
            margin: 0 0 20px;
            padding: 12px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid var(--border);
            color: #0f172a;
            font-size: 14px;
        }

        .actions {
            display: grid;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 14px;
            transition: transform 0.15s ease, box-shadow 0.2s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            color: #0f172a;
            border: 1px solid var(--border);
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 6px 18px rgba(20, 87, 71, 0.25);
        }

        .help {
            margin-top: 14px;
            font-size: 12px;
            color: var(--muted);
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="top">
            <div class="icon"><i class="fa-solid fa-circle-exclamation"></i></div>
            <h1><?php echo htmlspecialchars($title); ?></h1>
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>

        <div class="content">
            <div class="booking-note">
                Booking: <strong><?php echo $eventTitle; ?></strong> (ID #<?php echo (int) $booking_id; ?>)
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="/EventManagementSystem/public/client/payment/checkout?booking_id=<?php echo urlencode((string) $booking_id); ?>">
                    <i class="fa-solid fa-credit-card"></i> Pay Again
                </a>
                <a class="btn btn-secondary" href="/EventManagementSystem/public/client/bookings/view?id=<?php echo urlencode((string) $booking_id); ?>">
                    <i class="fa-solid fa-receipt"></i> Open This Booking
                </a>
            </div>

            <div class="help">You can retry payment now or review the booking details first.</div>
        </div>
    </div>
</body>

</html>
