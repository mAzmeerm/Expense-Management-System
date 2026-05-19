<?php
include("function.php");
require_login();

$loggedInUser = (int) $_SESSION['UserID'];

$userSql = "SELECT EmployeeID, Name, Email, Role FROM employee WHERE EmployeeID = ? LIMIT 1";
$userStmt = mysqli_prepare($dbconn, $userSql);
mysqli_stmt_bind_param($userStmt, "i", $loggedInUser);
mysqli_stmt_execute($userStmt);
$userResult = mysqli_stmt_get_result($userStmt);
$user = mysqli_fetch_assoc($userResult);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

if ($user['Role'] === 'Admin') {
    header("Location: AdminDashboard.php");
    exit();
}

$staffName = $user['Name'] ?: 'Employee';

$statsSql = "SELECT
        COUNT(*) AS total_count,
        COALESCE(SUM(CASE WHEN Status = 'Pending' THEN 1 ELSE 0 END), 0) AS pending_count,
        COALESCE(SUM(CASE WHEN Status = 'Approved' THEN 1 ELSE 0 END), 0) AS approved_count,
        COALESCE(SUM(CASE WHEN Status = 'Rejected' THEN 1 ELSE 0 END), 0) AS rejected_count,
        COALESCE(SUM(CASE WHEN Status = 'Approved' THEN Amount ELSE 0 END), 0) AS approved_total
    FROM expenseclaim
    WHERE EmployeeID = ?";
$statsStmt = mysqli_prepare($dbconn, $statsSql);
mysqli_stmt_bind_param($statsStmt, "i", $loggedInUser);
mysqli_stmt_execute($statsStmt);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($statsStmt));

$recentSql = "SELECT
        c.ClaimID,
        c.Description,
        c.Amount,
        c.ClaimDate,
        c.Status,
        COALESCE(cat.CategoryName, 'No Category') AS CategoryName
    FROM expenseclaim c
    LEFT JOIN expensecategory cat ON c.CategoryID = cat.CategoryID
    WHERE c.EmployeeID = ?
    ORDER BY c.ClaimDate DESC, c.ClaimID DESC
    LIMIT 5";
$recentStmt = mysqli_prepare($dbconn, $recentSql);
mysqli_stmt_bind_param($recentStmt, "i", $loggedInUser);
mysqli_stmt_execute($recentStmt);
$recent = mysqli_stmt_get_result($recentStmt);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'EmployeeDashboard.php';
        include 'EmployeeSidebar.php';
        ?>

        <div class="main-content">
            <header>
                <strong>Employee Dashboard</strong>
                <span>Welcome, <?= $staffName ?></span>
            </header>

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
                                <?php while ($row = mysqli_fetch_assoc($recent)): ?>
                                    <tr>
                                        <td><?= $row['ClaimID'] ?></td>
                                        <td><?= $row['Description'] ?></td>
                                        <td><?= $row['CategoryName'] ?></td>
                                        <td><?= money($row['Amount']) ?></td>
                                        <td><?= date('Y-m-d', strtotime($row['ClaimDate'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= strtolower($row['Status']) ?>">
                                                <?= $row['Status'] ?>
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
</body>

</html>
