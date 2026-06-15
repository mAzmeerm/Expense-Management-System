<?php
session_start();
include("dbconn.php");
include("function.php");
$loggedInUser = $_SESSION['UserID'];
if ($loggedInUser == '' or !isset($loggedInUser)) {
    header("Location: login.php");
    exit();
}
$sqlAdmin = "SELECT * FROM employee WHERE EmployeeID = '$loggedInUser'";
$queryAdmin = mysqli_query($dbconn, $sqlAdmin) or die("Error: "
    . mysqli_error($dbconn));
if ($row = mysqli_fetch_assoc($queryAdmin)) {
    $adminName = $row['Name']; 
    $adminEmail = $row['Email'];
    $adminRole = $row['Role'];
    $adminDepartmentID = $row['DepartmentID']; 
    $adminPhone = $row['PhoneNum'];
} else {
    $adminName = "Admin";
    $adminEmail = "";
    $adminRole = "";
    $adminDepartmentID = "";
    $adminPhone = "";
}

$sql2 = "SELECT DepartmentName FROM department WHERE DepartmentID = '$adminDepartmentID'";
$query2 = mysqli_query($dbconn, $sql2) or die("Error: "
    . mysqli_error($dbconn));
if ($row2 = mysqli_fetch_assoc($query2)) {
    $adminDepartmentName = $row2['DepartmentName'];
} else {
    $adminDepartmentName = "";
}
?>

<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Profile</title>
</head>
<body>
    <div class="layout">
        <?php
        $activePage = 'AdminProfile.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Settings', $adminName); ?>

            <div class="mn-content">
                <div class="container">
                    <div class="card">
                        <h3>Admin Profile</h3>
                        <p>Manage your account profile.</p>
                        <form method="post" action="AdminProfileProcess.php">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($adminName); ?>">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($adminPhone); ?>">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($adminEmail); ?>">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" placeholder="Enter new password">

            
                        <label for="department">Department: </label>
                        <select id="department" name="department">
                            <?php
                            $sqlDepartments = "SELECT * FROM department";
                            $queryDepartments = mysqli_query($dbconn, $sqlDepartments) or die("Error: " . mysqli_error($dbconn));
                            while ($rowDept = mysqli_fetch_assoc($queryDepartments)) {
                                $selected = ($rowDept['DepartmentID'] == $adminDepartmentID) ? 'selected' : '';
                                echo "<option value='" . $rowDept['DepartmentID'] . "' $selected>" . $rowDept['DepartmentName'] . "</option>";
                            }
                            ?>
                        </select>
                        <a href="AdminProfileProcess.php?action=update" class="btn btn-primary" type="submit" style="margin-top: 15px;">Save Changes</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</html>