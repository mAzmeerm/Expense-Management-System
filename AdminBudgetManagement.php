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

// 3. Fetch all matching budgets
$sqlBudget = "SELECT b.*, d.DepartmentName
              FROM budget b
              JOIN department d ON b.DepartmentID = d.DepartmentID
              WHERE d.DepartmentName LIKE '%$search%'
              OR b.Year LIKE '%$search%'
              OR b.Description LIKE '%$search%'
              ORDER BY d.DepartmentName ASC";

$budgets = mysqli_query($dbconn, $sqlBudget) or die("Error: " . mysqli_error($dbconn));
?>

<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Budget Management</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminBudgetManagement.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">

            <div class="container"> 
                <div class="card">

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <h3>Budget Management</h3>
                        <a href="AdminAddBudget.php" class="btn btn-primary" style="text-decoration:none;">
                            + Add New Budget
                        </a>
                    </div>

                    <form class="searchbar" method="get" style="display: flex; align-items: flex-end; gap: 0.5rem; margin-bottom: 1.5rem;">
                        <div style="flex: 1;">
                            <label>Search budget:</label>
                            <input type="text" name="search" placeholder="Department or year" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </div>
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a class="btn btn-secondary" href="AdminBudgetManagement.php" style="text-decoration: none;">Reset</a>
                    </form>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Year</th>
                                    <th>Allocated</th>
                                    <th>Spent</th>
                                    <th>Remaining</th>
                                    <th>Description</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                if (mysqli_num_rows($budgets) > 0) {
                                    while ($budget = mysqli_fetch_assoc($budgets)) {
                                ?>
                                        <tr>
                                            <td><?= htmlspecialchars($budget['DepartmentName']) ?></td>
                                            <td><?= htmlspecialchars($budget['Year']) ?></td>
                                            <td><?= money($budget['AllocatedAmount']) ?></td>
                                            <td><?= money($budget['SpentAmount']) ?></td>
                                            <td><?= money($budget['RemainAmount']) ?></td>
                                            <td><?= htmlspecialchars($budget['Description']) ?></td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">No budget found.</td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

</html>