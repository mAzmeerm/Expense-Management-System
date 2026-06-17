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
            <img src="logo.png" alt="AdadasSport Logo" width="200" height="200">
            <p style="font-family: 'Playfair Display', serif;font-style: italic;font-size: 1.85rem;color: white;">AdadasSport Expense Management System</p>
        </div>
        <div class="login-panel">
            <div class="card login-card">
                <div class="login-header" style="text-align: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.75rem; font-weight: 700; color: var(--color-nav); margin-bottom: 0.25rem;">
                        Expense Claims Portal
                    </h2>
                    <p style="font-size: 0.95rem; color: var(--color-muted); margin: 0;">
                        Secure Employee & Admin Claims
                    </p>
                </div>
                <form action="loginProcess.php" method="post">
                    <?php show_alert(); ?>
                    <div style="display: flex; gap: 24px; margin-top: .5rem; align-items: center; justify-content: center; width: 100%;">

                        <label style="margin-bottom: 10px; margin-top: 5px; font-weight: normal; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <span class="menu-item-wrapper" style="display: inline-flex; align-items: center;">
                                <input type="radio" name="role" value="Staff" checked style="width: auto; margin: 0;">
                                <img src="IconEmployee2.svg" alt="Employee" width="16" height="16" style="margin-right: 3px;">Employee
                            </span>
                        </label>

                        <label style=" margin-bottom: 10px; margin-top: 5px; font-weight: normal; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <span class="menu-item-wrapper" style="display: inline-flex; align-items: center;">
                                <input type="radio" name="role" value="Admin" style="width: auto; margin: 0;">
                                <img src="IconAdmin.svg" alt="Admin" width="16" height="16" style="margin-right: 3px;">Admin
                            </span>
                        </label>
                    </div>
                    <div class="input-box" style="margin-top: 15px">
                        <input class="login-input" type="email" id="Email" name="Email" required>
                        <label for="Email" class="login-label">Email Address</label>
                    </div>
                    <div class="input-box password-container" style="margin-top: 25px">
                        <input class="login-input" type="password" id="Password" name="Password" required>
                        <label for="Password" class="login-label">Password</label>
                        <span id="toggle">Show </span>
                    </div>

                    <script src="script.js"></script>
                    <button class="btn btn-login" style="width:100%;margin-top:1.2rem;" name="login">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>