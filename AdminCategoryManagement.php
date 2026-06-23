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
$search = isset($_GET['search']) ? mysqli_real_escape_string($dbconn, $_GET['search']) : '';
//Fetch all matching Category
$sqlCategory = "SELECT * 
                FROM expensecategory 
                WHERE CategoryName 
                LIKE '%$search%' 
                ORDER BY CategoryID ASC";
$categories = mysqli_query($dbconn, $sqlCategory) or die("Error: " . mysqli_error($dbconn));
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
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


                        <form class="searchbar" id="searchForm" method="get" onsubmit="event.preventDefault();">
                            <div style="flex: 1; display: flex; gap: 15px; align-items: flex-end;">
                                <div style="flex: 2;">
                                    <label style="margin-top: 0;">Search Category:</label>
                                    <input type="text" id="tableSearch" name="search"
                                        placeholder="Search by category name" value="<?= htmlspecialchars($search) ?>"
                                        oninput="liveSearch()">
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('tableSearch').value=''; liveSearch();" style="margin-top: auto;">Reset</button>
                        </form>

                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Category ID</th>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($categories) > 0) {
                                        while ($row = mysqli_fetch_assoc($categories)) { ?>
                                            <tr>
                                                <td><?php echo $row['CategoryID']; ?></td>
                                                <td><?php echo $row['CategoryName']; ?></td>
                                                <td>
                                                    <a href="AdminCategoryProcess.php?action=edit&id=<?php echo $row['CategoryID']; ?>" class="btn btn-primary">Edit</a>
                                                    <a href="AdminCategoryProcess.php?action=delete&id=<?php echo $row['CategoryID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="3" style="text-align:center;">No categories found.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                        </div>

</html>