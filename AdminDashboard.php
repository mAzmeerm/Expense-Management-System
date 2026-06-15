<?php
session_start();
include("dbconn.php");
include("function.php");
require_login();

$loggedInUser = $_SESSION['UserID'];
$sql = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));

if ($row = mysqli_fetch_assoc($query)) {
    $adminName = $row['Name'];
} else {
    $adminName = "Admin";
}

//filter selected year
$selectedYear = isset($_GET['year']) ? mysqli_real_escape_string($dbconn, $_GET['year']) : date('Y');

// count stats based on year choosen
$budgetQuery = "SELECT 
                    COALESCE(SUM(AllocatedAmount), 0) total_budget, 
                    COALESCE(SUM(SpentAmount), 0) total_spent, 
                    COALESCE(SUM(RemainAmount), 0) total_remaining 
                FROM budget 
                WHERE Year = '$selectedYear'";
$budget = mysqli_fetch_assoc(mysqli_query($dbconn, $budgetQuery));

//count pending
$pending = mysqli_fetch_assoc(mysqli_query($dbconn, "SELECT COUNT(*) c FROM expenseclaim WHERE status='Pending'"))['c'];

// filter according to claim year
$chartSql = "SELECT d.DepartmentName, COALESCE(SUM(c.Amount), 0) total 
             FROM department d 
             LEFT JOIN employee e ON d.DepartmentID = e.DepartmentID 
             LEFT JOIN expenseclaim c ON e.EmployeeID = c.EmployeeID 
                AND c.Status = 'Approved' 
                AND YEAR(c.ClaimDate) = '$selectedYear' 
             GROUP BY d.DepartmentID 
             ORDER BY total DESC";

$chart = mysqli_query($dbconn, $chartSql) or die("Error chart query: " . mysqli_error($dbconn));
$max = 1;
$chart_rows = [];
while ($r = mysqli_fetch_assoc($chart)) {
    $chart_rows[] = $r;
    if ($r['total'] > $max) {
        $max = $r['total'];
    }
}

// filter recent claim
$recent = mysqli_query($dbconn, "SELECT c.*, e.Name, cat.CategoryName, d.DepartmentName
                                 FROM expenseclaim c 
                                 JOIN employee e ON c.EmployeeID = e.EmployeeID 
                                 JOIN expensecategory cat ON c.CategoryID = cat.CategoryID 
                                 JOIN department d ON e.DepartmentID = d.DepartmentID 
                                 ORDER BY c.ClaimDate DESC LIMIT 5");
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminDashboard.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Dashboard', $adminName); ?>

            <div class="container">

                <div class="stats-grid">
                    <div class="stat-card" style="border-left-color:#16a34a">
                        <h4>Total Budget (<?= $selectedYear ?>)</h4>
                        <h3><?= money($budget['total_budget']) ?></h3>
                    </div>
                    <div class="stat-card" style="border-left-color:#dc2626">
                        <h4>Total Expenses (<?= $selectedYear ?>)</h4>
                        <h3><?= money($budget['total_spent']) ?></h3>
                    </div>
                    <div class="stat-card" style="border-left-color:#2563eb">
                        <h4>Total Balance (<?= $selectedYear ?>)</h4>
                        <h3><?= money($budget['total_remaining']) ?></h3>
                    </div>
                    <div class="stat-card" style="border-left-color:#d97706">
                        <h4>Pending Claims</h4>
                        <h3><?= $pending ?></h3>
                    </div>
                </div>

                <div class="grid-2">

                    <div class="card">
                        <h3>Approved Spending by Department</h3>
                        <label for="year">Year: </label>
                        <!-- to ask browser to immediate refresh page after receive value -->
                        <select id="year" name="year" onchange="window.location.href='?year=' + this.value;">
                            <?php
                            $sqlYear = "SELECT DISTINCT b.Year FROM budget b ORDER BY b.Year DESC";
                            $queryYear = mysqli_query($dbconn, $sqlYear) or die("Error fetch years: " . mysqli_error($dbconn));

                            while ($rowYear = mysqli_fetch_assoc($queryYear)) {
                                if ($rowYear['Year'] == $selectedYear) {
                                    $selected = 'selected';
                                } else {
                                    $selected = '';
                                }
                                echo "<option value='" . $rowYear['Year'] . "' $selected>" . $rowYear['Year'] . "</option>";
                            }
                            ?>
                        </select>

                        <div class="table-responsive">
                            <div class="chart-bars">
                                <?php
                                //Get total number of departments in the array
                                $total_departments = count($chart_rows);
                                //Loop through each department using a standard for loop
                                for ($i = 0; $i < $total_departments; $i++) {
                                    $row = $chart_rows[$i];
                                    // Calculate height percentage
                                    $height_percentage = ($row['total'] / $max) * 100;

                                    // Standard if-else 
                                    if ($height_percentage < 4) {
                                        $h = 4;
                                    } else {
                                        $h = $height_percentage;
                                    }

                                    // print
                                    echo '<div class="chart-bar" style="height:' . $h . '%">';
                                    echo '    <span>' . money($row['total']) . '</span>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            <div class="chart-labels">
                                <?php
                                //Get the total number of items in the array
                                $total_departments = count($chart_rows);

                                //Loop through each item using a standard for loop
                                for ($i = 0; $i < $total_departments; $i++) {
                                    $row = $chart_rows[$i];

                                    //Print the container with the department name directly
                                    echo '<div>' . $row['DepartmentName'] . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Recent Claims</h3>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Claim ID</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                                        <tr>
                                            <td><?= $r['ClaimID'] ?></td>
                                            <td><?= $r['Name'] ?></td>
                                            <td><?= $r['DepartmentName'] ?></td>
                                            <td><?= $r['CategoryName'] ?></td>
                                            <td><?= money($r['Amount']) ?></td>
                                            <td><?= date('Y-m-d', strtotime($r['ClaimDate'])) ?></td>
                                            <td>
                                                <span class="badge badge-<?= strtolower($r['Status']) ?>">
                                                    <?= $r['Status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

</html>