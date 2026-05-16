<?php
session_start();
include("dbconn.php");

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($dbconn, $_POST['Email']);
    $password = $_POST['Password'];
    $role = mysqli_real_escape_string($dbconn, $_POST['role']);
    $sql = "SELECT * FROM employee WHERE Email='$email' AND Role='$role' AND Password='$password'";
    $query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
            $_SESSION['UserID'] = $row['EmployeeID'];
            $_SESSION['Email'] = $row['Email'];
            $_SESSION['Role'] = $row['Role'];
            $_SESSION['Password'] = $row['Password'];
            if ($_SESSION['Role'] == "Admin") {
                header("Location: AdminDashboard.php");
                exit();
            } else if ($_SESSION['Role'] == "Staff") {
                header("Location: EmployeeDashboard.php");
                exit();
            }
    } 
    else {
        $_SESSION['login_error'] = '<div class="alert"> Invalid email, password, or role. Please try again.</div>';
        header("Location: login.php");
        exit();
    }
}
mysqli_close($dbconn);
?>