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

</body>

</html>