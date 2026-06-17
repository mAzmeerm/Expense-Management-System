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
$selectedYear = isset($_GET['YearFilter']) ? mysqli_real_escape_string($dbconn, $_GET['YearFilter']) : '';


$sqlBudget = "SELECT b.*, d.DepartmentName
              FROM budget b
              JOIN department d ON b.DepartmentID = d.DepartmentID
              WHERE (d.DepartmentName LIKE '%$search%'
                 OR b.Year LIKE '%$search%'
                 OR b.Description LIKE '%$search%')";


if ($selectedYear !== '') {
    $sqlBudget .= " AND b.Year = '$selectedYear'";
}

// FIX: Append the sorting rule at the absolute end of the query string
$sqlBudget .= " ORDER BY b.BudgetID DESC";

$budgets = mysqli_query($dbconn, $sqlBudget) or die("Error processing budget query: " . mysqli_error($dbconn));

//Fetch all unique years for dropdown checklist options
$sqlYear = "SELECT DISTINCT Year FROM budget ORDER BY Year ASC";
$queryYear = mysqli_query($dbconn, $sqlYear) or die("Error fetch year: " . mysqli_error($dbconn));
?>

<html>

<head>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <title>Admin Budget Management</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminBudgetManagement.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Budget Management', $adminName); ?>
            <div class="mn-content">

                <div class="container">
                    <?php show_alert(); ?>
                    <div class="card">

                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>Budget Management</h3>
                            <a href="AdminBudgetProcess.php?action=add" class="btn btn-primary" style="text-decoration:none;">
                                + Add New Budget
                            </a>
                        </div>

                        <form class="searchbar" id="searchForm" method="get" action="AdminBudgetManagement.php">
                            <div style="flex: 1; display: flex; gap: 15px; align-items: flex-end;">

                                <div style="flex: 2;">
                                    <label style="margin-top: 0;">Search budgets:</label>
                                    <input type="text" id="tableSearch" name="search" placeholder="Search by department, year, description..."
                                        value="<?= htmlspecialchars($search) ?>"
                                        oninput="liveSearch()">
                                </div>

                                <div style="flex: 1;">
                                    <label style="margin-top: 0;">Filter by Year:</label>
                                    <select id="YearFilter" name="YearFilter" onchange="this.form.submit();">
                                        <option value="">-- All Years --</option>
                                        <?php
                                        while ($rowYear = mysqli_fetch_assoc($queryYear)) {
                                            $YearVal = $rowYear['Year'];
                                            $selected = ($YearVal == $selectedYear) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($YearVal) . "' $selected>" . htmlspecialchars($YearVal) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <a class="btn btn-secondary" href="AdminBudgetManagement.php" style="text-decoration: none; margin-top: auto;">Reset</a>
                        </form>

                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Budget ID</th>
                                        <th>Department</th>
                                        <th>Year</th>
                                        <th>Allocated</th>
                                        <th>Spent</th>
                                        <th>Remaining</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($budgets) > 0) {
                                        while ($budget = mysqli_fetch_assoc($budgets)) {
                                    ?>
                                            <tr>
                                                <td><?= htmlspecialchars($budget['BudgetID']) ?></td>
                                                <td><?= htmlspecialchars($budget['DepartmentName']) ?></td>
                                                <td><?= htmlspecialchars($budget['Year']) ?></td>
                                                <td><?= money($budget['AllocatedAmount']) ?></td>
                                                <td><?= money($budget['SpentAmount']) ?></td>
                                                <td><?= money($budget['RemainAmount']) ?></td>
                                                <td><?= htmlspecialchars($budget['Description']) ?></td>
                                                <td>
                                                    <a href="AdminBudgetProcess.php?action=edit&BudgetID=<?= $budget['BudgetID'] ?>" class="btn btn-secondary">Edit</a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="7" style="text-align:center;">No budget found.</td>
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
</body>

</html>