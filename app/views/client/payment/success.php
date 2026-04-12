<?php
if (!isset($transaction_id) || !isset($booking_id)) {
    header('Location: /EventManagementSystem/public/client/events');
    exit;
}

$isAdvanceComplete = isset($isAdvanceComplete) ? (bool) $isAdvanceComplete : false;
$remainingAdvance = isset($remainingAdvance) ? (float) $remainingAdvance : 0;
$paidAdvance = isset($paidAdvance) ? (float) $paidAdvance : 0;
$advanceTarget = isset($advanceTarget) ? (float) $advanceTarget : 0;
$nextInstallmentAmount = isset($nextInstallmentAmount) ? (float) $nextInstallmentAmount : 0;

$bookingDetailsUrl = '/EventManagementSystem/public/client/bookings/view?id=' . urlencode((string) $booking_id);
$nextInstallmentUrl = '/EventManagementSystem/public/client/payment/checkout?booking_id=' . urlencode((string) $booking_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f0fdf4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #1e293b;
            text-align: center;
            padding: 16px;
        }

        .success-card {
            background: white;
            padding: 36px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 520px;
            width: 100%;
        }

        .icon {
            font-size: 60px;
            color: #22c55e;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 10px;
        }

        p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        .tx-id {
            background: #f8fafc;
            padding: 10px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            color: #475569;
            margin-bottom: 14px;
            border: 1px solid #e2e8f0;
        }

        .progress {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 16px;
            text-align: left;
            font-size: 13px;
            color: #334155;
        }

        .progress b {
            color: #0f172a;
        }

        .soft {
            color: #0f766e;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .btn-row {
            display: grid;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            background: #246A55;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn:hover {
            background: #1a4d3e;
        }

        .btn-outline {
            background: #ffffff;
            border: 1px solid #d1d5db;
            color: #111827;
        }

        .btn-outline:hover {
            background: #f8fafc;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            80% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <div class="success-card">
        <div class="icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1>Payment Successful!</h1>
        <p>Your installment has been received successfully.</p>

        <div class="tx-id">
            TX ID: <?php echo htmlspecialchars($transaction_id); ?>
        </div>

        <div class="progress">
            <div><b>Online Advance Paid:</b> NPR <?php echo number_format($paidAdvance, 2); ?> / NPR <?php echo number_format($advanceTarget, 2); ?></div>
            <div><b>Remaining Online Advance:</b> NPR <?php echo number_format($remainingAdvance, 2); ?></div>
        </div>

        <?php if (!$isAdvanceComplete): ?>
            <p class="soft">NPR <?php echo number_format($remainingAdvance, 2); ?> is still left for online advance. You can pay the next installment now.</p>
        <?php else: ?>
            <p class="soft">Online advance is complete. Remaining 50% can be settled offline on event day.</p>
        <?php endif; ?>

        <div class="btn-row">
            <?php if (!$isAdvanceComplete): ?>
                <a href="<?php echo htmlspecialchars($nextInstallmentUrl); ?>" class="btn">Pay Next Installment (NPR <?php echo number_format($nextInstallmentAmount, 2); ?>)</a>
            <?php endif; ?>
            <a href="<?php echo htmlspecialchars($bookingDetailsUrl); ?>" class="btn btn-outline">Open This Booking</a>
        </div>

        <p style="margin-top:12px; font-size:12px; color:#64748b;">Use the buttons above to continue.</p>
    </div>

</body>

</html>
