<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Event Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f7f8fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 450px;
            padding: 45px 40px;
            border-radius: 6px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        }
        .header {
            margin-bottom: 35px;
        }
        .header h1 {
            font-size: 22px;
            color: #1a1e23;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .header p {
            font-size: 14px;
            color: #6b7280;
        }
        .form-group {
            margin-bottom: 22px;
        }
        .form-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .form-label-row label {
            font-size: 11px;
            font-weight: 700;
            color: #555c66;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .forgot-link {
            font-size: 12px;
            color: #1f6b52;
            text-decoration: none;
            font-weight: 700;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
        .form-control {
            width: 100%;
            padding: 14px 16px;
            background-color: #e9ecef;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            color: #495057;
            transition: background-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            background-color: #dee2e6;
        }
        .form-control::placeholder {
            color: #adb5bd;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #fac245; /* Yellow/Orange from screenshot */
            color: #212529; /* Dark text for contrast */
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        .btn-submit:hover {
            background-color: #f5b62b;
        }
        .btn-submit:active {
            transform: translateY(1px);
        }
        .error-message {
            background-color: #fee2e2;
            color: #ef4444;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #fca5a5;
        }
        .footer {
            margin-top: 35px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            padding-top: 25px;
            border-top: 1px solid #f3f4f6;
        }
        .footer a {
            color: #1f6b52;
            text-decoration: none;
            font-weight: 600;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
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
                <a href="#" class="forgot-link">Forgot Password?</a>
            </div>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-submit">
            Login <span>&rarr;</span>
        </button>
    </form>

    <div class="footer">
        Don't have an account? <a href="#">Register</a>
    </div>
</div>

</body>
</html>
