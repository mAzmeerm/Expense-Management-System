<?php
session_start();
include("dbconn.php");
include("function.php");
$loggedInUser = $_SESSION['UserID'];

if (!isset($_SESSION['UserID']) || $_SESSION['UserID'] === '') {
    header("Location: login.php");
    exit();
}
//test test

// Query to look up this specific employee
$sql = "SELECT e.*, d.DepartmentName 
        FROM employee e 
        JOIN department d ON e.DepartmentID = d.DepartmentID 
        WHERE e.EmployeeID = '$loggedInUser'";
$query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));

// Fetch the data record
if ($row = mysqli_fetch_assoc($query)) {
    // Redirect admins away from the employee view
    if ($row['Role'] === 'Admin') {
        header("Location: AdminDashboard.php");
        exit();
    }
    $employeeName = $row['Name'];
    $employeeEmail = $row['Email'];
    $employeePhone = $row['PhoneNum'];
    $employeeRole = $row['Role'];
    $employeeDept = $row['DepartmentName'];
} else {
    header("Location: login.php");
    exit();
}
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Employee Profile</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'EmployeeProfile.php';
        include 'EmployeeSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('My Profile', $employeeName); ?>

            <div class="mn-content">
                <div class="container">
                    <?php show_alert(); ?>
                    <div class="card">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>My Profile</h3>
                        </div>

                        <form method="post" action="EmployeeProfileProcess.php">

                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name"
                                value="<?php echo htmlspecialchars($employeeName); ?>" required>

                            <label for="phone">Phone Number:</label>
                            <input type="text" id="phone" name="phone"
                                value="<?php echo htmlspecialchars($employeePhone); ?>" required>

                            <label>Email:</label>
                            <input type="email" value="<?php echo htmlspecialchars($employeeEmail); ?>" disabled
                                style="background:#f4f4f4;">

                            <label>Department:</label>
                            <input type="text" value="<?php echo htmlspecialchars($employeeDept); ?>" disabled
                                style="background:#f4f4f4;">

                            <label>Role:</label>
                            <input type="text" value="<?php echo htmlspecialchars($employeeRole); ?>" disabled
                                style="background:#f4f4f4;">

                            <button type="submit" class="btn btn-primary" style="margin-top: 15px; width: 100%;">Save
                                Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>