<?php
if (!isset($transaction_id)) {
    header('Location: /EventManagementSystem/public/client/events');
    exit;
}
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
        body { font-family: 'Inter', sans-serif; background: #f0fdf4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #1e293b; text-align: center; }
        .success-card { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 400px; width: 100%; }
        .icon { font-size: 60px; color: #22c55e; margin-bottom: 20px; animation: scaleIn 0.5s ease-out; }
        h1 { font-size: 24px; margin: 0 0 10px; }
        p { color: #64748b; font-size: 14px; margin-bottom: 20px; line-height: 1.5; }
        .tx-id { background: #f8fafc; padding: 10px; border-radius: 8px; font-family: monospace; font-size: 12px; color: #475569; margin-bottom: 30px; border: 1px solid #e2e8f0;}
        .btn { display: inline-block; background: #246A55; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; transition: 0.2s; }
        .btn:hover { background: #1a4d3e; }
        @keyframes scaleIn { 0% { transform: scale(0); opacity: 0; } 80% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>

    <div class="success-card">
        <div class="icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1>Payment Successful!</h1>
        <p>Your payment has been processed successfully. The organizer will review your booking shortly.</p>
        
        <div class="tx-id">
            TX ID: <?php echo htmlspecialchars($transaction_id); ?>
        </div>

        <a href="/EventManagementSystem/public/client/bookings" class="btn">View My Bookings</a>
    </div>

</body>
</html>
