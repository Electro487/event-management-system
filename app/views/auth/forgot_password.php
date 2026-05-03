<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/auth-otp.css">
</head>
<body>
    <div class="container">
        <h1>Forgot Password?</h1>
        <p>Don't worry, it happens to the best of us. Enter your email below to reset your access.</p>
        
        <div id="api-status" style="display: none; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

        <form action="/EventManagementSystem/public/forgot-password" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="name@example.com" required>
            </div>
            <button type="submit" class="btn">
                Send Reset Link <span>&rarr;</span>
            </button>
        </form>

        <a href="/EventManagementSystem/public/login" class="back-link">&larr; Back to Login</a>
    </div>
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script>
        (function() {
            const form = document.querySelector('form');
            const submitBtn = form?.querySelector('.btn');
            const statusDiv = document.getElementById('api-status');
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

                const email = document.getElementById('email')?.value?.trim() || '';
                if (!email) return;

                // Disable button and show loading state
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Sending...';
                if (statusDiv) statusDiv.style.display = 'none';

                try {
                    const res = await window.emsApi.apiFetch('/api/v1/auth/forgot-password', {
                        method: 'POST',
                        body: { email }
                    });

                    if (res?.ok || res?.data?.email) {
                        showStatus('OTP sent successfully! Redirecting...', false);
                        setTimeout(() => {
                            window.location.href = '/EventManagementSystem/public/verify-otp';
                        }, 1200);
                    }
                } catch (err) {
                    console.error('API Forgot Password failed:', err);
                    showStatus(err.message);
                    
                    // Re-enable button if it failed
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        })();
    </script>
</body>
</html>
