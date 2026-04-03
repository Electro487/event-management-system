<?php
if (!isset($booking_id)) {
    header('Location: /EventManagementSystem/public/client/events');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #fef2f2; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #1e293b; text-align: center; }
        .cancel-card { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 400px; width: 100%; border-top: 4px solid #ef4444; }
        .icon { font-size: 60px; color: #ef4444; margin-bottom: 20px; animation: shake 0.5s ease-in-out; }
        h1 { font-size: 24px; margin: 0 0 10px; color: #b91c1c; }
        p { color: #64748b; font-size: 14px; margin-bottom: 30px; line-height: 1.5; }
        .btn { display: inline-block; background: #1e293b; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; transition: 0.2s; margin-bottom: 10px; width: calc(100% - 48px); box-sizing: border-box; }
        .btn:hover { background: #0f172a; }
        .btn-outline { background: transparent; color: #475569; border: 1px solid #cbd5e1; }
        .btn-outline:hover { background: #f8fafc; }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
    </style>
</head>
<body>

    <div class="cancel-card">
        <div class="icon">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <h1>Payment Failed</h1>
        <p>Something went wrong, or the payment was cancelled. Your booking remains unpaid.</p>
        
        <a href="/EventManagementSystem/public/client/payment/checkout?booking_id=<?php echo htmlspecialchars($booking_id); ?>" class="btn">Try Again</a>
        <br>
        <a href="/EventManagementSystem/public/client/bookings" class="btn btn-outline" style="margin-top: 10px;">Return to Bookings</a>
    </div>

</body>
</html>
