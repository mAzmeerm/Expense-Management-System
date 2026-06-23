<?php
session_start();
include("dbconn.php");
include("function.php");
require_login();
$loggedInUser = $_SESSION['UserID'];
$sql = "SELECT Name, Role FROM employee WHERE EmployeeID = '$loggedInUser'";
$query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));

if ($row = mysqli_fetch_assoc($query)) {
    if ($row['Role'] === 'Admin') {
        header("Location: AdminDashboard.php");
        exit();
    }
    $staffName = $row['Name'];
} else {
    session_destroy();
    header("Location: login.php");
    exit();
}

$stats = mysqli_fetch_assoc(mysqli_query($dbconn, "SELECT COUNT(*) AS total_count, COALESCE(SUM(CASE WHEN Status = 'Pending' THEN 1 ELSE 0 END), 0) AS pending_count, 
                            COALESCE(SUM(CASE WHEN Status = 'Approved' THEN 1 ELSE 0 END), 0) AS approved_count, 
                            COALESCE(SUM(CASE WHEN Status = 'Rejected' THEN 1 ELSE 0 END), 0) AS rejected_count, 
                            COALESCE(SUM(CASE WHEN Status = 'Approved' THEN Amount ELSE 0 END), 0) AS approved_total FROM expenseclaim WHERE EmployeeID = '$loggedInUser'"));

$recent = mysqli_query($dbconn, "SELECT c.ClaimID, c.Description, c.Amount, c.ClaimDate, c.Status, 
                        COALESCE(cat.CategoryName, 'No Category') AS CategoryName FROM expenseclaim c LEFT JOIN expensecategory cat ON c.CategoryID = cat.CategoryID 
                        WHERE c.EmployeeID = '$loggedInUser' ORDER BY c.ClaimDate DESC, c.ClaimID DESC LIMIT 5");
?>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Employee Dashboard</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'EmployeeDashboard.php';
        include 'EmployeeSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Employee Dashboard', $staffName); ?>

            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card" style="border-left-color:#2563eb">
                        <h4>Total Claims</h4>
                        <h3><?= $stats['total_count'] ?? 0 ?></h3>
                    </div>

                    <div class="stat-card" style="border-left-color:#d97706">
                        <h4>Pending Claims</h4>
                        <h3><?= $stats['pending_count'] ?? 0 ?></h3>
                    </div>

                    <div class="stat-card" style="border-left-color:#16a34a">
                        <h4>Approved Claims</h4>
                        <h3><?= $stats['approved_count'] ?? 0  ?></h3>
                    </div>

                    <div class="stat-card" style="border-left-color:#dc2626">
                        <h4>Rejected Claims</h4>
                        <h3><?= $stats['rejected_count'] ?? 0 ?></h3>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card" style="border-left-color:#0b4f8a">
                        <h4>Total Approved Amount</h4>
                        <h3><?= money($stats['approved_total'] ?? 0) ?></h3>
                    </div>
                </div>

                <div class="card">
                    <h3>Recent Claims</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Claim ID</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent) > 0): ?>
                                    <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                                        <tr>
                                            <td><?= $r['ClaimID'] ?></td>
                                            <td><?= $r['Description'] ?></td>
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
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center; color:#64748b; padding:1.5rem;">
                                            No expense claims found yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>