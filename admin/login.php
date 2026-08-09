<?php
require_once __DIR__ . '/../includes/functions.php';

// Already logged in as admin
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . SITE_URL . '/admin/index');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Email and password are required.';
        } else {
            $stmt = db()->prepare('SELECT id, name, email, password, is_admin FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && $user['is_admin'] && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id']   = $user['id'];
                $_SESSION['admin_name'] = $user['name'];
                // Also set user_id so require_admin() / current_user() work
                $_SESSION['user_id']    = $user['id'];
                header('Location: ' . SITE_URL . '/admin/index');
                exit;
            } else {
                $error = 'Invalid credentials or you do not have admin access.';
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
    <title>Admin Login — Fabloom</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f4f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 16px rgba(0,0,0,.10);
            padding: 2.5rem 2rem;
        }
        .logo {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #1C1917;
        }
        .logo-text span { color: #C0282A; }
        .logo-sub {
            font-size: .75rem;
            color: #6b7280;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        h2 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1C1917;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            border-radius: 6px;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1.25rem;
        }
        label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .35rem;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: .65rem .9rem;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            font-size: .95rem;
            color: #1C1917;
            outline: none;
            transition: border-color .2s;
            margin-bottom: 1.1rem;
        }
        input:focus { border-color: #C0282A; }
        .btn-login {
            width: 100%;
            padding: .75rem;
            background: #C0282A;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .3px;
            transition: background .2s;
        }
        .btn-login:hover { background: #a81f21; }
        .back-link {
            text-align: center;
            margin-top: 1.25rem;
            font-size: .85rem;
        }
        .back-link a { color: #6b7280; text-decoration: none; }
        .back-link a:hover { color: #C0282A; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="card">
        <div class="logo">
            <div class="logo-text">Fab<span>loom</span></div>
            <div class="logo-sub">Admin Panel</div>
        </div>
        <h2>Sign in to continue</h2>

        <?php if ($error): ?>
            <div class="alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= csrf_field() ?>
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required autofocus placeholder="admin@fabloom.com">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <!-- Admin accounts live in the same `users` table as customers, so the
             account reset flow works here: the emailed link sets
             users.password and leaves is_admin alone. There is no separate
             admin reset to maintain. -->
        <div class="back-link" style="margin-top:1rem">
            <a href="<?= h(SITE_URL) ?>/account/forgot-password">Forgot your password?</a>
        </div>

        <div class="back-link"><a href="<?= h(SITE_URL) ?>">← Back to Website</a></div>
    </div>
</div>
</body>
</html>
