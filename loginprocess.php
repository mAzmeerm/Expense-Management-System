<?php
session_start();
include("dbconn.php");
include("function.php");

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($dbconn, $_POST['Email']);
    $password = $_POST['Password'];
    $role = mysqli_real_escape_string($dbconn, $_POST['role']);
    $sql = "SELECT * FROM employee WHERE Email='$email' AND Role='$role'";
    $query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));
    $row = mysqli_fetch_assoc($query);
    if ($row['Status'] == "Inactive") {
         set_alert('error', '<span class="menu-item-wrapper" style = "display: inline-flex; align-items: center; width: 100%; box-sizing: border-box; white-space: normal;"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Your account was inactive , please contact admin to reactivate.</span>', 'login.php');
    }

    $loginOk = false;

    if ($row) {
        $storedPassword = $row['Password'];

        if (is_hashed($storedPassword)) {
            // Normal case: stored value is already a bcrypt hash.
            $loginOk = verify_password($password, $storedPassword);
        } else {
            // Legacy case: stored value is still plaintext (old account).
            // Check it the old way, then upgrade it to a hash right now.
            if ($password === $storedPassword) {
                $loginOk = true;

                $newHash = mysqli_real_escape_string($dbconn, hash_password($password));
                $employeeID = mysqli_real_escape_string($dbconn, $row['EmployeeID']);
                mysqli_query($dbconn, "UPDATE employee SET Password = '$newHash' WHERE EmployeeID = '$employeeID'");
                $row['Password'] = $newHash; // keep $row in sync for the session below
            }
        }
    }

    if ($loginOk) {
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
    } else {
        set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Invalid email, password, or role. Please try again.</span>', 'login.php');
    }
}
mysqli_close($dbconn);
