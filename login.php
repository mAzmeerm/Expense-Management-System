<html>
<head>
    <title>Login Page</title>
</head>
<body>
    <h1>Welcome Back!</h1>
    <h2>Sign in to continue to your account</h2>
    <form action="login_process.php" method="post">
        <label for="Login">Login as:</label> <br>
        <input type="radio" name="role" value="Employee">Employee
        <input type="radio" name="role" value="Admin">Admin
        <br>
        <label for="Email">Username:</label>
            <input type="text" id="Email" name="Email" required>
            <br>
            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required>
            <br>
            <input type="submit" value="Login">
        </form>
    </body>
</html>