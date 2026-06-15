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
// 2. Process search keywords securely
$search = isset($_GET['search']) ? mysqli_real_escape_string($dbconn, $_GET['search']) : '';
// 3. Fetch all matching Department
$sqlCategory = "SELECT * FROM department WHERE DepartmentName LIKE '%$search%' ORDER BY DepartmentID ASC";
$categories = mysqli_query($dbconn, $sqlCategory) or die("Error: " . mysqli_error($dbconn));
?>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Department Management</title>
</head>
<body>
    <div class="layout">
        <?php
        $activePage = 'AdminDepartmentManagement.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Department Management', $adminName); ?>

            <div class="mn-content">
                <div class="container">
                    <?= show_alert(); ?>
                    <div class="card">

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>Department Management</h3>
                            <a href="AdminDepartmentProcess.php?action=add" class="btn btn-primary" style="text-decoration:none;">
                                + Add New Department
                            </a>
                        </div>

                        <form class="searchbar" method="get">
                            <div style="flex: 1;">
                                <label style="margin-top: 0;">Search category:</label>
                                <input type="text" name="search" placeholder="Name of Department" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Search</button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='AdminDepartmentManagement.php'">Reset</button>
                        </form>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Department ID</th>
                                    <th>Department Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                                    <tr>
                                        <td><?php echo $row['DepartmentID']; ?></td>
                                        <td><?php echo $row['DepartmentName']; ?></td>
                                        <td>
                                            <a href="AdminDepartmentProcess.php?action=edit&id=<?php echo $row['DepartmentID']; ?>" class="btn btn-primary">Edit</a>
                                            <a href="AdminDepartmentProcess.php?action=delete&id=<?php echo $row['DepartmentID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this department?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
</html>
