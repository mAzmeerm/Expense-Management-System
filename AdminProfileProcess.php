<?php
session_start();
include("dbconn.php");
include("function.php");
require_login();
$loggedInUser = $_SESSION['UserID'];
$sqlAdmin = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$queryAdmin = mysqli_query($dbconn, $sqlAdmin) or die("Error: " . mysqli_error($dbconn));
if ($row = mysqli_fetch_assoc($queryAdmin)) {
    $adminName = $row['Name'];
} else {
    $adminName = "Admin";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name         = mysqli_real_escape_string($dbconn, $_POST['name']);
    $phone        = mysqli_real_escape_string($dbconn, $_POST['phone']);
    $email        = mysqli_real_escape_string($dbconn, $_POST['email']);
    $departmentID = mysqli_real_escape_string($dbconn, $_POST['department']);
    $password     = $_POST['password'];
    $sqlUpdate = "UPDATE employee 
                  SET Name = '$name', 
                      PhoneNum = '$phone', 
                      Email = '$email', 
                      DepartmentID = '$departmentID'";
    if (!empty($password)) {
        $sqlUpdate .= ", Password = '" . mysqli_real_escape_string($dbconn, $password) . "'";
    }
    $sqlUpdate .= " WHERE EmployeeID = '$loggedInUser'";
    if (mysqli_query($dbconn, $sqlUpdate)) {
        set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Success" width="20" height="20" style="margin-right: 5px;"> Profile updated successfully.</span>', 'AdminProfile.php');

    } else {
        set_alert('error', 'Error updating profile: ' . mysqli_error($dbconn), 'AdminProfile.php');
    }
}else {
    header("Location: AdminProfile.php");
    exit();
}
?>