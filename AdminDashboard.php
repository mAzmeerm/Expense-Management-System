<?php
session_start();
include("dbconn.php");
$loggedInUser = $_SESSION['UserID'];

// Query to look up this specific employee
$sql = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));

// Fetch the data record
if ($row = mysqli_fetch_assoc($query)) {
    $adminName = $row['Name'];
} else {
    $adminName = "Admin";
}
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="layout"><?php include "AdminSidebar.php"; ?><div class="content">
            <header> <strong> Admin Dashboard</strong> <span> Welcome, <?php echo $adminName; ?> </span></header>
        </div>
    </div>
</body>

</html>