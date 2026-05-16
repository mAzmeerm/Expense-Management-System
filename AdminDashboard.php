<?php
session_start();
include("dbconn.php");
include("function.php");
$loggedInUser = $_SESSION['UserID'];

// Query to look up this specific employee
$sql = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));

// Fetch the data record
if ($row = mysqli_fetch_assoc($query)) {
    $adminName = $row['Name'];
} else {
    $adminName = "Admin";
}

$budget = mysqli_fetch_assoc(mysqli_query($dbconn, "SELECT COALESCE(SUM(AllocatedAmount),0) total_budget, COALESCE(SUM(SpentAmount),0) total_spent, COALESCE(SUM(RemainAmount),0) total_remaining FROM budget"));
$pending = mysqli_fetch_assoc(mysqli_query($dbconn, "SELECT COUNT(*) c FROM expenseclaim WHERE status='Pending'"))['c'];
$chart = mysqli_query($dbconn, "SELECT cat.CategoryName, COALESCE(SUM(c.amount),0) total FROM expensecategory cat LEFT JOIN expenseclaim c ON cat.CategoryID=c.CategoryID AND c.Status='Approved' GROUP BY cat.CategoryID ORDER BY total DESC");
$max = 1;
$chart_rows = [];
while ($r = mysqli_fetch_assoc($chart)) {
    $chart_rows[] = $r;
    if ($r['total'] > $max) $max = $r['total'];
}
$recent = mysqli_query($dbconn, "SELECT c.*, e.Name, cat.CategoryName FROM expenseclaim c JOIN employee e ON c.EmployeeID=e.EmployeeID JOIN expensecategory cat ON c.CategoryID=cat.CategoryID ORDER BY c.ClaimDate DESC LIMIT 5");
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="layout">
        <?php include 'AdminSidebar.php'; ?>

        <div class="main-content">
            <header>
                <strong>Admin Dashboard</strong>
                <span>Welcome, <?php echo $adminName; ?></span>
            </header>

            <div class="container">

                <div class="stats-grid">
                    <div class="stat-card" style="border-left-color:#16a34a">
                        <h4>Total Budget</h4>
                        <h3><?= money($budget['total_budget']) ?></h3>
                    </div>
                    <div class="stat-card" style="border-left-color:#dc2626">
                        <h4>Total Expenses</h4>
                        <h3><?= money($budget['total_spent']) ?></h3>
                    </div>
                    <div class="stat-card" style="border-left-color:#2563eb">
                        <h4>Total Balance</h4>
                        <h3><?= money($budget['total_remaining']) ?></h3>
                    </div>
                    <div class="stat-card" style="border-left-color:#d97706">
                        <h4>Pending Claims</h4>
                        <h3><?= $pending ?></h3>
                    </div>
                </div>

                <div class="grid-2">

                    <div class="card">
                        <h3>Approved Spending by Category</h3>
                        <div class="chart-bars">
                            <?php foreach ($chart_rows as $row): $h = max(4, ($row['total'] / $max) * 100); ?>
                                <div class="chart-bar" style="height:<?= $h ?>%">
                                    <span><?= money($row['total']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="chart-labels">
                            <?php foreach ($chart_rows as $row): ?>
                                <div><?= $row['CategoryName'] ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div> <div class="card">
                        <h3>Recent Claims</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                                    <tr>
                                        <td><?= $r['Name'] ?></td>
                                        <td><?= $r['CategoryName']?></td>
                                        <td><?= money($r['Amount']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= strtolower($r['Status']) ?>">
                                                <?= $r['Status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div> </div> </div> </div> </div> </body>

</html>