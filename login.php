<?php
session_start(); 
include("dbconn.php");
include("function.php");
?>
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
                    <?php show_alert(); ?>

                    <label for="Login">Login as:</label> <br>
                    <div style="display: flex; gap: 1.5rem; margin-top: .5rem; align-items: center;">
                        <label style="margin: 0; font-weight: normal; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="role" value="Staff" checked style="width: auto; margin: 0;"> Employee
                        </label>
                        <label style="margin: 0; font-weight: normal; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="role" value="Admin" style="width: auto; margin: 0;"> Admin
                        </label>
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