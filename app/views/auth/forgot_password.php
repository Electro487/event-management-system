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
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

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
</body>
</html>
