<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | e.PLAN</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Permanent+Marker&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/register.css">
</head>

<body>

    <div class="split-layout">
        <!-- Left Branding Panel -->
        <section class="branding-panel">
            <div class="branding-content">
                <div class="logo">
                    <img src="/EventManagementSystem/public/assets/images/logo-white.png" alt="e.PLAN Logo" class="logo-img">
                </div>

                <h1 class="hero-title">
                    Craft Your<br>
                    Perfect Event<br>
                    Experience.
                </h1>

                <p class="hero-subtitle">
                    Discover curated packages or customize every
                    element to fit your vision. Tailored event
                    planning, just the way you imagined.
                </p>
            </div>
        </section>

        <!-- Right Form Panel -->
        <section class="form-panel">
            <div class="form-container">
                <h2 class="form-title">Create Account</h2>
                <p class="form-subtitle">Enter your credentials to access the management dashboard.</p>

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="error-message" style="color: #d9534f; background: #f9ebeb; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">
                        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div id="api-status" style="display: none; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

                <form action="/EventManagementSystem/public/register" method="POST" class="register-form">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">FIRST NAME</label>
                            <input type="text" id="first_name" name="first_name" placeholder="Subham" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">LAST NAME</label>
                            <input type="text" id="last_name" name="last_name" placeholder="Joshi" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">EMAIL ADDRESS</label>
                        <input type="email" id="email" name="email" placeholder="subham@gmail.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">PASSWORD</label>
                        <input type="password" id="password" name="password" placeholder="••••••••••••" required>
                    </div>

                    <!-- OTP task will be handled by collaborator -->

                    <button type="submit" class="btn-submit">
                        Register Account <span class="arrow-icon">&gt;</span>
                    </button>

                </form>

                <div class="auth-footer">
                    <p>Already have an account? <a href="/EventManagementSystem/public/login" class="login-link">Sign in here</a></p>
                </div>
            </div>
        </section>
    </div>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script>
        (function() {
            const form = document.querySelector('.register-form');
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

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const first_name = document.getElementById('first_name')?.value?.trim() || '';
                const last_name = document.getElementById('last_name')?.value?.trim() || '';
                const email = document.getElementById('email')?.value?.trim() || '';
                const password = document.getElementById('password')?.value || '';

                if (!first_name || !last_name || !email || !password) return;

                // UI Loading State
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Creating Account...';
                if (statusDiv) statusDiv.style.display = 'none';

                console.log('%c[API Auth] Attempting Registration...', 'color: #3498db; font-weight: bold;');

                try {
                    const res = await window.emsApi.apiFetch('/api/v1/auth/register', {
                        method: 'POST',
                        body: { first_name, last_name, email, password }
                    });

                    console.log('%c[API Auth] Registration Success!', 'color: #27ae60; font-weight: bold;', res);

                    if (res?.data?.otp_required) {
                        showStatus('Account created! Redirecting to verification...', false);
                        setTimeout(() => {
                            window.location.href = '/EventManagementSystem/public/verify-otp';
                        }, 1200);
                    }
                } catch (err) {
                    console.warn('%c[API Auth] Registration Failed:', 'color: #e67e22; font-weight: bold;', err.message);
                    
                    let displayMsg = err.message;
                    
                    // The API returns errors in err.payload.error.meta (from ApiResponse::error)
                    const errorObj = err.payload?.error;
                    if (errorObj && errorObj.meta) {
                        const metaErrors = errorObj.meta;
                        // Get the first error message from the meta object
                        const firstError = Object.values(metaErrors)[0];
                        if (firstError) displayMsg = firstError;
                    }
                    
                    showStatus(displayMsg);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        })();
    </script>
</body>

</html>
