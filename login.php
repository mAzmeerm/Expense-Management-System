<html>
<head>
    <title>Login Page</title>
        <form action="login_process.php" method="post">
            <input type =" radio" name="role" value="Employee">Employee
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