<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bakery Expenses Record System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-bg-text">
            <h1>Welcome to Bakery Expenses <br>Record System </h1>
            <p>Track your daily expenses, manage employees, and keep your bakery running smoothly.</p>
        </div>

        <div class="login-form">
            <h2>Login to Your Account</h2>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Invalid username or password</div>
            <?php endif; ?>
            <form action="login_process.php" method="post">

                <div class="mb-3 input-icon">
                    <label for="username" class="form-label">Username</label>
                    <span class="icon">👤</span>
                    <input type="text" class="form-control " id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="mb-3 input-icon">
                    <label for="password" class="form-label">Password</label>
                    <span class="icon">🔒</span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="login-options">
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>


            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>