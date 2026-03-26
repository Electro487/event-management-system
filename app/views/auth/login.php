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

    <form action="<?php echo defined('URL_ROOT') ? URL_ROOT . '/login' : '/EventManagementSystem/public/login'; ?>" method="POST">
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

</body>
</html>
