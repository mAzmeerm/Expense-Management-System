<?php
session_start();
include("dbconn.php");
include("function.php");
$loggedInUser = $_SESSION['UserID'];

if ($loggedInUser == '' or !isset($loggedInUser)) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($dbconn, $_POST['name']);
    $phone = mysqli_real_escape_string($dbconn, $_POST['phone']);
   


    if (mysqli_query($dbconn, $sqlUpdate)) {
        set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Profile updated successfully.</span>', 'EmployeeProfile.php');
    } else {
        set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error updating profile: ' . mysqli_error($dbconn) . '</span>', 'EmployeeProfile.php');
    }
} else {

    header("Location: EmployeeProfile.php");
    exit();
}