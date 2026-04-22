<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/auth-otp.css">
</head>

<body>
    <div class="container">
        <h1>Verify Your Identity</h1>
        <?php
        $email = $_SESSION['otp_email'] ?? 'your email';
        $parts = explode('@', $email);
        $masked_email = substr($parts[0], 0, 2) . '***@' . ($parts[1] ?? '');
        ?>
        <p>We've sent a 6-digit verification code to <span class="email-preview"><?php echo $masked_email; ?></span>.
            Please enter it below to secure your account.</p>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="error"><?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="/EventManagementSystem/public/verify-otp" method="POST" id="otp-form">
            <div class="otp-inputs">
                <input type="text" name="otp[]" maxlength="1" required autofocus>
                <input type="text" name="otp[]" maxlength="1" required>
                <input type="text" name="otp[]" maxlength="1" required>
                <input type="text" name="otp[]" maxlength="1" required>
                <input type="text" name="otp[]" maxlength="1" required>
                <input type="text" name="otp[]" maxlength="1" required>
            </div>
            <button type="submit" class="btn">Verify Code</button>
        </form>

        <div class="resend-info">
            DIDN'T RECEIVE THE CODE?<br>
            <span id="timer">Resend available in 02:45</span>
        </div>
        <a class="resend-link" id="resend-btn" style="display:none;">Resend code</a>

        <script>
            const inputs = document.querySelectorAll('.otp-inputs input');
            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (e.target.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            let timeLeft = 165;
            const timerDisplay = document.getElementById('timer');
            const resendBtn = document.getElementById('resend-btn');

            const countdown = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    timerDisplay.style.display = 'none';
                    resendBtn.style.display = 'inline-block';
                } else {
                    let minutes = Math.floor(timeLeft / 60);
                    let seconds = timeLeft % 60;
                    timerDisplay.textContent = `Resend available in ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    timeLeft--;
                }
            }, 1000);
        </script>
    </div>
</body>

</html>