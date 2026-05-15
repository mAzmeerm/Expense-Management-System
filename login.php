<html>
<head>
    <link rel="stylesheet" href="login.css">
    <title>Login Page</title>
</head>
<body>
    <div class= "login-wrapper">
    <div class="login-brand"><h1>AdadasSport Enterprise</h1><p>Manage expenses, budgets, employees and approvals securely.</p></div>
    <div class="login-panel"><div class="card login-card">
    <h1>Welcome Back!</h1>
    <p style="color:#64748b;margin:8px 0 20px;">Sign in to continue</p>
    <form action="loginprocess.php" method="post">
        <label for="Login">Login as:</label> <br>
        <input type="radio" name="role" value="Employee">Employee
        <input type="radio" name="role" value="Admin">Admin
        <br>
        <label for="Email">Email:</label>
            <input type="text" id="Email" name="Email" required>
            <br>
            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required> <br>
            <button class="btn btn-primary" style="width:100%;margin-top:1.2rem;" name = "login" >Login</button>
        </form>
</div></div>   
</div>
    </body>
</html>