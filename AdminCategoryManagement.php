<?php
session_start();
include("dbconn.php");
include("function.php");
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
// 3. Fetch all matching Category
$sqlCategory = "SELECT * FROM expensecategory WHERE CategoryName LIKE '%$search%' ORDER BY CategoryID ASC";
$categories = mysqli_query($dbconn, $sqlCategory) or die("Error: " . mysqli_error($dbconn));
?>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Category Management</title>
</head>
<body>
    <div class="layout">
        <?php
        $activePage = 'AdminCategoryManagement.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Category Management', $adminName); ?>

            <div class="mn-content">
                <div class="container">
                    <?php show_alert(); ?>
                    <div class="card">

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>Category Management</h3>
                            <a href="AdminCategoryProcess.php?action=add" class="btn btn-primary" style="text-decoration:none;">
                                + Add New Category
                            </a>
                        </div>

                        <form class="searchbar" method="get">
                            <div style="flex: 1;">
                                <label style="margin-top: 0;">Search category:</label>
                                <input type="text" name="search" placeholder="Name of category" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Search</button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='AdminCategoryManagement.php'">Reset</button>
                        </form>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Category ID</th>
                                    <th>Category Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                                    <tr>
                                        <td><?php echo $row['CategoryID']; ?></td>
                                        <td><?php echo $row['CategoryName']; ?></td>
                                        <td>
                                            <a href="AdminCategoryProcess.php?action=edit&id=<?php echo $row['CategoryID']; ?>" class="btn btn-primary">Edit</a>
                                            <a href="AdminCategoryProcess.php?action=delete&id=<?php echo $row['CategoryID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
</html>
