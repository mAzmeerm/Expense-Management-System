<?php
session_start();
include("dbconn.php");
$loggedInUser = $_SESSION['UserID'];

$sql = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));

if ($row = mysqli_fetch_assoc($query)) {
    $staffName = $row['Name'];
} else {
    $staffName = "Employee";
}
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Employee Dashboard</title>
</head>

<body>
    <div class="login-wrapper">
        <div class="card login-card" style="max-width:600px; margin: 3rem auto;">
            <h1>Employee Dashboard</h1>
            <p style="margin: 1rem 0;">Welcome, <?php echo htmlspecialchars($staffName); ?>.</p>
            <p>You are logged in as a Staff member.</p>
            <p><a href="logout.php" class="btn btn-primary" style="display:inline-block; margin-top:1rem; text-decoration:none;">Logout</a></p>
        </div>
    </div>
</body>

</html>