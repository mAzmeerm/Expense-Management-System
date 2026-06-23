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
$selectedYear = isset($_GET['yearFilter']) ? mysqli_real_escape_string($dbconn, $_GET['yearFilter']) : '';

// PAGINATION
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sqlCount = "SELECT COUNT(*) as total FROM budget b
             JOIN department d ON b.DepartmentID = d.DepartmentID
             WHERE (d.DepartmentName LIKE '%$search%'
             OR b.Year LIKE '%$search%'
             OR b.Description LIKE '%$search%')";

if ($selectedYear !== '') {
    $sqlCount .= " AND b.Year = '$selectedYear'";
}
$countResult = mysqli_query($dbconn, $sqlCount) or die("Count Error: " . mysqli_error($dbconn));
$countRow = mysqli_fetch_assoc($countResult);
$totalPages = ceil($countRow['total'] / $limit);

// FETCH MATCHING BUDGETS
$sqlBudget = "SELECT b.*, d.DepartmentName
              FROM budget b
              JOIN department d ON b.DepartmentID = d.DepartmentID
              WHERE (d.DepartmentName LIKE '%$search%'
              OR b.Year LIKE '%$search%'
              OR b.Description LIKE '%$search%')";

if ($selectedYear !== '') {
    $sqlBudget .= " AND b.Year = '$selectedYear'";
}

$sqlBudget .= " ORDER BY b.BudgetID DESC LIMIT $offset, $limit";
$budgets = mysqli_query($dbconn, $sqlBudget) or die("Query Error: " . mysqli_error($dbconn));

// FETCH DISTINCT YEARS FOR DROPDOWN
$sqlYearOpt = "SELECT DISTINCT Year FROM budget ORDER BY Year ASC";
$queryYear = mysqli_query($dbconn, $sqlYearOpt) or die("Error fetch Year: " . mysqli_error($dbconn));
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

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>Budget Management</h3>
                            <a href="AdminBudgetProcess.php?action=add" class="btn btn-primary" style="text-decoration:none;">
                                + Add New Budget
                            </a>
                        </div>

                        <script src="script.js" defer></script>

                        <form class="searchbar" id="searchForm" onsubmit="event.preventDefault();">
                            <div style="flex: 1; display: flex; gap: 15px; align-items: flex-end;">

                                <div style="flex: 2;">
                                    <label style="margin-top: 0;">Search Budget:</label>
                                    <input type="text" id="tableSearch" placeholder="Search by department, description, year..." oninput="liveSearch()">
                                </div>

                                <div style="flex: 1;">
                                    <label style="margin-top: 0;">Filter by year:</label>
                                    <select id="yearFilter" onchange="document.getElementById('tableSearch').value = this.value; liveSearch();">
                                        <option value="">-- All years --</option>
                                        <?php
                                        while ($rowYear = mysqli_fetch_assoc($queryYear)) {
                                            $yearVal = $rowYear['Year'];
                                            echo "<option value='" . htmlspecialchars($yearVal) . "'>" . htmlspecialchars($yearVal) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                            <button type="button" class="btn btn-secondary"
                                onclick="document.getElementById('tableSearch').value=''; document.getElementById('yearFilter').value=''; liveSearch();"
                                style="margin-top: auto;">
                                Reset
                            </button>
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
                                            <td colspan="8" style="text-align:center;">No budget found.</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <?php
                        if (function_exists('show_pagination')) {
                            show_pagination($page, $totalPages, $search);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>