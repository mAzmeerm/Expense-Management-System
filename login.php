<?php
session_start(); ?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Login Page</title>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-brand">
            <h1>AdadasSport Enterprise</h1>
            <p>Manage expenses, budgets, employees and approvals securely.</p>
        </div>
        <div class="login-panel">
            <div class="card login-card">
                <h1>Welcome Back!</h1>
                <p style="color:#64748b;margin:8px 0 20px;">Sign in to continue</p>
                <form action="loginprocess.php" method="post">
                    <?php if (isset($_SESSION['login_error'])): ?>
                        <?php
                       echo  $_SESSION['login_error'];
                        unset($_SESSION['login_error']);
                        ?>
                    <?php endif; ?>
                    <label for="Login">Login as:</label> <br>
                    <div style="display:flex;gap:1rem;margin-top:.5rem;"><label style="margin:0;">
                            <input type="radio" name="role" value="Staff" checked style="width:auto;">Employee</label>
                        <label style="margin:0;">
                            <input type="radio" name="role" value="Admin" style="width:auto;">Admin </label>
                    </div>
                    <br>
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="Email" required placeholder="example@gmail.com">
                    <br>
                    <label for="Password">Password:</label>
                    <input type="password" id="Password" name="Password" required placeholder="••••••••"> <br>
                    <button class="btn btn-primary" style="width:100%;margin-top:1.2rem;" name="login">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>