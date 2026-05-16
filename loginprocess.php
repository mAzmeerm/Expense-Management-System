<?php
session_start();
include("dbconn.php");

if (isset($_POST['login'])) {
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $role = $_POST['role'];
    $sql = "SELECT * FROM employee WHERE Email='$email' AND Password='$password' AND Role='$role'";
    $query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $_SESSION['UserID'] = $row['UserID'];
        $_SESSION['Email'] = $row['Email'];
        $_SESSION['Role'] = $row['Role'];
        if ($_SESSION['Role'] == "Admin") {
            header("Location: AdminDashboard.php");
        } else if ($_SESSION['Role'] == "Staff") {
            header("Location: EmployeeDashboard.php");
        }
    } else {

        $_SESSION['login_error'] = '<div class="alert alert-danger"> Invalid email, password, or role. Please try again.</div>';
        header("Location: login.php");
        exit();
    }
}
mysqli_close($dbconn);
