<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Event Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo defined('URL_ROOT') ? URL_ROOT : '/EventManagementSystem/public'; ?>/assets/css/login.css">
</head>
<body>

<div class="login-card">
    <div class="header">
        <h1>Welcome Back</h1>
        <p>Please enter your details to access your dashboard.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success-message" style="color: #2d5a27; background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div id="api-status" style="display: none; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

    <form id="login-form" action="<?php echo defined('URL_ROOT') ? URL_ROOT . '/login' : '/EventManagementSystem/public/login'; ?>" method="POST">
        <div class="form-group">
            <div class="form-label-row">
                <label for="email">Email / Username</label>
            </div>
            <input type="text" id="email" name="email" class="form-control" placeholder="name@e-plan.com" required>
        </div>

        <div class="form-group">
            <div class="form-label-row">
                <label for="password">Password</label>
                <a href="/EventManagementSystem/public/forgot-password" class="forgot-link">Forgot Password?</a>
            </div>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-submit">
            Login <span>&rarr;</span>
        </button>
    </form>

    <div class="footer">
        Don't have an account? <a href="/EventManagementSystem/public/register">Register</a>
    </div>
</div>

<script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
<script>
    (function() {
        const form = document.getElementById('login-form');
        const submitBtn = form?.querySelector('.btn-submit');
        const statusDiv = document.getElementById('api-status');
        if (!form || !window.emsApi) return;

        function showStatus(msg, isError = true) {
            if (!statusDiv) return;
            statusDiv.textContent = msg;
            statusDiv.style.display = 'block';
            statusDiv.style.background = isError ? '#f9ebeb' : '#e8f5e9';
            statusDiv.style.color = isError ? '#d9534f' : '#2d5a27';
        }

        let isSyncing = false;
        form.addEventListener('submit', async function(e) {
            if (isSyncing) return; // Allow standard form submit to proceed
            e.preventDefault();

            const email = document.getElementById('email')?.value?.trim() || '';
            const password = document.getElementById('password')?.value || '';
            if (!email || !password) return;

            // UI Loading State
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Verifying...';
            if (statusDiv) statusDiv.style.display = 'none';

            console.log('%c[API Auth] Attempting Login...', 'color: #3498db; font-weight: bold;');

            try {
                const res = await window.emsApi.apiFetch('/api/v1/auth/login', {
                    method: 'POST',
                    body: { email, password }
                });

                console.log('%c[API Auth] Login Success!', 'color: #27ae60; font-weight: bold;', res);

                const token = res?.data?.token;
                if (!token) throw new Error('Login succeeded but no token returned.');

                window.emsApi.setToken(token);
                showStatus('Authentication successful! Redirecting...', false);

                // No longer need to submit the form. The AuthBridge will handle the PHP session via cookie.
                const user = res?.data?.user;
                const role = user?.role || 'client';
                
                setTimeout(() => {
                    if (role === 'admin') window.location.href = '/EventManagementSystem/public/admin/dashboard';
                    else if (role === 'organizer') window.location.href = '/EventManagementSystem/public/organizer/dashboard';
                    else window.location.href = '/EventManagementSystem/public/client/home';
                }, 800);

            } catch (err) {
                console.warn('%c[API Auth] Failed:', 'color: #e67e22; font-weight: bold;', err.message);
                
                // Always show API error instead of falling back
                showStatus(err.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    })();
</script>

</body>
</html>
