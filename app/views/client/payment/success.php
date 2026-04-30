<?php
$booking_id = $_GET['booking_id'] ?? 0;
$session_id = $_GET['session_id'] ?? '';

if (!$booking_id || !$session_id) {
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
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
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
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-out;
        }
        .success-card.loaded {
            opacity: 1;
            transform: translateY(0);
        }

        .icon {
            font-size: 60px;
            color: #22c55e;
            margin-bottom: 20px;
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

        #verifying {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #246A55;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>

<body>

    <div id="verifying">
        <div class="spinner"></div>
        <p>Verifying your payment with Stripe...</p>
    </div>

    <div class="success-card" id="success-card" style="display: none;">
        <div class="icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1>Payment Successful!</h1>
        <p>Your installment has been received successfully.</p>

        <div class="tx-id" id="display-tx"></div>

        <div class="progress">
            <div><b>Online Advance Paid:</b> NPR <span id="display-paid"></span> / NPR <span id="display-target"></span></div>
            <div><b>Remaining Online Advance:</b> NPR <span id="display-remaining"></span></div>
        </div>

        <p class="soft" id="display-message"></p>

        <div class="btn-row">
            <a href="#" id="next-btn" class="btn" style="display: none;">Pay Next Installment</a>
            <a href="#" id="view-btn" class="btn btn-outline">Open This Booking</a>
        </div>

        <p style="margin-top:12px; font-size:12px; color:#64748b;">Use the buttons above to continue.</p>
    </div>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script>
        (async function() {
            const bookingId = <?php echo (int)$booking_id; ?>;
            const sessionId = "<?php echo htmlspecialchars($session_id); ?>";

            try {
                if (!window.emsApi) throw new Error('API Client not loaded');

                const res = await window.emsApi.apiFetch('/api/v1/payments/confirm', {
                    method: 'POST',
                    body: { booking_id: bookingId, session_id: sessionId }
                });

                const data = res.data;
                document.getElementById('verifying').style.display = 'none';
                
                const card = document.getElementById('success-card');
                card.style.display = 'block';
                setTimeout(() => card.classList.add('loaded'), 50);

                // Populate UI
                document.getElementById('display-tx').textContent = 'TX ID: ' + (data.transaction_id || sessionId);
                document.getElementById('display-paid').textContent = data.paid_advance.toLocaleString();
                document.getElementById('display-target').textContent = data.advance_target.toLocaleString();
                document.getElementById('display-remaining').textContent = data.remaining_advance.toLocaleString();
                
                const msg = document.getElementById('display-message');
                if (data.is_advance_complete) {
                    msg.textContent = "Online advance is complete. Any tiny remaining balance and the final 50% will be settled offline on your event day.";
                } else {
                    msg.textContent = `NPR ${data.remaining_advance.toLocaleString()} is still left for online advance. You can pay the next installment now.`;
                    const nextBtn = document.getElementById('next-btn');
                    nextBtn.href = `/EventManagementSystem/public/client/payment/checkout?booking_id=${bookingId}`;
                    nextBtn.textContent = `Pay Next Installment (NPR ${data.next_installment_amount.toLocaleString()})`;
                    nextBtn.style.display = 'block';
                }

                document.getElementById('view-btn').href = `/EventManagementSystem/public/client/bookings/view?id=${bookingId}`;

            } catch (err) {
                console.error('Verification failed:', err);
                window.location.href = `/EventManagementSystem/public/client/payment/error?booking_id=${bookingId}&reason=verification_failed`;
            }
        })();
    </script>
</body>

</html>
