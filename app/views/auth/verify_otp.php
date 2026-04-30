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

        <div id="api-status" style="display: none; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

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

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script>
        (function() {
            const form = document.getElementById('otp-form');
            const submitBtn = form?.querySelector('.btn');
            const statusDiv = document.getElementById('api-status');
            const inputs = document.querySelectorAll('.otp-inputs input');
            if (!form || !window.emsApi) return;

            function showStatus(msg, isError = true) {
                if (!statusDiv) return;
                statusDiv.textContent = msg;
                statusDiv.style.display = 'block';
                statusDiv.style.background = isError ? '#f9ebeb' : '#e8f5e9';
                statusDiv.style.color = isError ? '#d9534f' : '#2d5a27';
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const otp = Array.from(inputs).map(i => i.value).join('');
                const email = "<?php echo $_SESSION['otp_email'] ?? ''; ?>";
                const otp_type = "<?php echo $_SESSION['otp_type'] ?? 'registration'; ?>";

                if (otp.length !== 6 || !email) return;

                // UI Loading State
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Verifying...';
                if (statusDiv) statusDiv.style.display = 'none';

                console.log('%c[API Auth] Verifying OTP...', 'color: #3498db; font-weight: bold;');

                try {
                    const res = await window.emsApi.apiFetch('/api/v1/auth/verify-otp', {
                        method: 'POST',
                        body: { email, otp, otp_type }
                    });

                    console.log('%c[API Auth] OTP Success!', 'color: #27ae60; font-weight: bold;', res);

                    if (res?.data?.verified) {
                        showStatus('Code verified! Redirecting to login...', false);
                        setTimeout(() => {
                            if (otp_type === 'password_reset') {
                                window.location.href = '/EventManagementSystem/public/reset-password';
                            } else {
                                // Direct redirect to login with success flag
                                // We don't use form.submit() here because the API already cleared the OTP in DB,
                                // so the MVC verification would fail if called again.
                                window.location.href = '/EventManagementSystem/public/login?success=verified';
                            }
                        }, 1200);
                    }
                } catch (err) {
                    console.warn('%c[API Auth] OTP Failed:', 'color: #e67e22; font-weight: bold;', err.message);
                    
                    if (err.status && err.status < 500) {
                        showStatus(err.message);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    } else {
                        showStatus('API Service Error: ' + err.message);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                }
            });

            // Input navigation logic (already in file, kept for reference)
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
        })();
    </script>
</body>

</html>