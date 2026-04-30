<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/auth-otp.css">
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <p>Enter your new password below.</p>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="/EventManagementSystem/public/reset-password" method="POST">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Min 6 characters" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script>
        (function() {
            const form = document.querySelector('form');
            if (!form || !window.emsApi) return;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const password = document.getElementById('password')?.value || '';
                const confirm_password = document.getElementById('confirm_password')?.value || '';
                const email = "<?php echo $_SESSION['otp_email'] ?? ''; ?>";

                if (!password || !confirm_password || !email) return;

                try {
                    const res = await window.emsApi.apiFetch('/api/v1/auth/reset-password', {
                        method: 'POST',
                        body: { email, password, confirm_password }
                    });

                    if (res?.ok || res?.data?.password_reset) {
                        window.location.href = '/EventManagementSystem/public/login';
                    }
                } catch (err) {
                    console.error('API Reset Password failed, falling back to MVC:', err);
                    form.submit();
                }
            });
        })();
    </script>
</body>
</html>
