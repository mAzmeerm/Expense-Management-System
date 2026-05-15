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
            header("Location: admin_dashboard.php");
        } else {
            header("Location: employee_dashboard.php");
        }
    } else {

        echo "<script>
    alert('Invalid email, password, or role. Please try again.');
    window.location.href = 'login.php'; // JavaScript moves the page AFTER the user clicks OK
</script>";
        exit();
    }
}
mysqli_close($dbconn);
?>