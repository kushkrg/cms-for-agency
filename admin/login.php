<?php
/**
 * Evolvcode CMS - Admin Login Page
 */

require_once __DIR__ . '/../includes/config.php';

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    header('Location: ' . ADMIN_URL);
    exit;
}

$error = '';

// Handle login form
if (Security::isPost()) {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!Security::validateCSRFToken($token)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        // Handle OTP Verification
        if (isset($_POST['verify_otp'])) {
            $username = $_POST['username'] ?? '';
            $otp = $_POST['otp'] ?? '';
            
            $result = Auth::verifyOtp($username, $otp);
            if ($result['success']) {
                Auth::redirectToIntended(ADMIN_URL);
                exit;
            } else {
                $error = $result['message'];
                $showOtpForm = true;
            }
        } 
        // Handle Resend OTP
        elseif (isset($_POST['resend_otp'])) {
            $username = $_POST['username'] ?? '';
            $result = Auth::resendOtp($username);
            if ($result['success']) {
                $message = $result['message'];
                $showOtpForm = true;
            } else {
                $error = $result['message'];
                $showOtpForm = true; // Still show OTP form
            }
        }
        // Handle Initial Login
        else {
            $username = Security::clean($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $result = Auth::login($username, $password);
            
            if ($result['success']) {
                if (isset($result['status']) && $result['status'] === 'REQUIRE_OTP') {
                    $showOtpForm = true;
                    // Prepare for OTP step
                } else {
                    Auth::redirectToIntended(ADMIN_URL);
                    exit;
                }
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= e(getSetting('site_name', 'Evolvcode')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ... existing styles ... */
        :root {
            --color-black: #000000;
            --color-white: #FFFFFF;
            --color-gray-50: #FAFAFA;
            --color-gray-100: #F5F5F5;
            --color-gray-200: #EEEEEE;
            --color-gray-300: #E0E0E0;
            --color-gray-400: #BDBDBD;
            --color-gray-500: #9E9E9E;
            --color-gray-600: #757575;
            --color-gray-700: #616161;
            --color-gray-800: #424242;
            --color-gray-900: #212121;
            --color-error: #F44336;
            --color-primary: #000000;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--color-gray-50);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        
        .login-box {
            background: var(--color-white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 40px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 3px;
            color: var(--color-black);
        }
        
        .login-title {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .login-subtitle {
            text-align: center;
            color: var(--color-gray-600);
            font-size: 14px;
            margin-bottom: 32px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--color-gray-700);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 14px;
            border: 1px solid var(--color-gray-300);
            border-radius: 8px;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--color-black);
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--color-black);
            color: var(--color-white);
        }
        
        .btn:hover {
            background: var(--color-gray-800);
            transform: translateY(-1px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-link {
            background: none;
            color: var(--color-gray-600);
            text-transform: none;
            text-decoration: underline;
            padding: 0;
            width: auto;
            font-weight: 500;
            font-size: 0.9em;
            margin-top: 15px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-link:hover {
            background: none;
            color: var(--color-black);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #FFEBEE;
            color: #C62828;
            border: 1px solid #EF9A9A;
        }
        
        .alert-success {
            background: #E8F5E9;
            color: #2E7D32;
            border: 1px solid #A5D6A7;
        }
        
        .back-link {
            text-align: center;
            margin-top: 24px;
        }
        
        .back-link a {
            color: var(--color-gray-600);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s ease;
        }
        
        .back-link a:hover {
            color: var(--color-black);
        }
        
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .otp-inputs input {
            text-align: center;
            letter-spacing: 2px;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <span class="logo-text">EVOLVCODE</span>
            </div>
            
            <?php if (isset($showOtpForm) && $showOtpForm): ?>
                <h1 class="login-title">Verify Identity</h1>
                <p class="login-subtitle">Enter the 6-digit code sent to your email</p>
            <?php else: ?>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to access the admin panel</p>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($message) && $message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= e($message) ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($showOtpForm) && $showOtpForm): ?>
                <!-- OTP Verification Form -->
                <form method="POST" action="">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="verify_otp" value="1">
                    <input type="hidden" name="username" value="<?= e($username) ?>">
                    
                    <div class="form-group">
                        <label for="otp" class="form-label" style="text-align: center;">One-Time Password</label>
                        <input type="text" name="otp" id="otp" class="form-control" 
                               placeholder="123456" maxlength="6" pattern="[0-9]{6}" required autofocus 
                               style="text-align: center; letter-spacing: 5px; font-size: 24px; padding: 10px;">
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-check-circle"></i> Verify & Login
                    </button>
                </form>
                
                <form method="POST" action="">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="resend_otp" value="1">
                    <input type="hidden" name="username" value="<?= e($username) ?>">
                    <button type="submit" class="btn btn-link">
                        Resend OTP Code
                    </button>
                </form>
                
            <?php else: ?>
                <!-- Standard Login Form -->
                <form method="POST" action="">
                    <?= Security::csrfField() ?>
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Username or Email</label>
                        <input type="text" name="username" id="username" class="form-control" 
                               placeholder="Enter your username" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Enter your password" required>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="<?= e(SITE_URL) ?>">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
