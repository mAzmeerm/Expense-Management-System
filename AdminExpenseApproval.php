<?php
session_start();
include("dbconn.php");
include("function.php");
require_login();

$loggedInUser = $_SESSION['UserID'];

// 1. Fetch Admin Name
$sqlAdmin = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$queryAdmin = mysqli_query($dbconn, $sqlAdmin) or die("Error: " . mysqli_error($dbconn));

if ($row = mysqli_fetch_assoc($queryAdmin)) {
    $adminName = $row['Name'];
} else {
    $adminName = "Admin";
}

// 2. Process search keywords and status filter securely
$search = isset($_GET['search']) ? mysqli_real_escape_string($dbconn, $_GET['search']) : '';
$selectedStatus = isset($_GET['statusFilter']) ? mysqli_real_escape_string($dbconn, $_GET['statusFilter']) : '';

// 3. Build SQL query merging both Search keywords AND Status Filter conditions
$sqlClaims = "SELECT c.*, e.Name, cat.CategoryName, d.DepartmentName
              FROM expenseclaim c 
              JOIN employee e ON c.EmployeeID = e.EmployeeID 
              JOIN expensecategory cat ON c.CategoryID = cat.CategoryID 
              JOIN department d ON e.DepartmentID = d.DepartmentID
              WHERE (e.Name LIKE '%$search%' 
                 OR cat.CategoryName LIKE '%$search%' 
                 OR c.Status LIKE '%$search%' 
                 OR d.DepartmentName LIKE '%$search%')";

// If a specific dropdown filter status is selected, append it to the query
if ($selectedStatus !== '') {
    $sqlClaims .= " AND c.Status = '$selectedStatus'";
}

$sqlClaims .= " ORDER BY c.ClaimID DESC";
$claims = mysqli_query($dbconn, $sqlClaims) or die("Error: " . mysqli_error($dbconn));

// 4. Fetch distinct status variants for our dropdown dynamically
$sqlStatus = "SELECT DISTINCT status FROM expenseclaim ORDER BY status ASC";
$queryStatus = mysqli_query($dbconn, $sqlStatus) or die("Error fetch status: " . mysqli_error($dbconn));
?>

<html>

<head>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <title>Admin Expense Approval</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminExpenseApproval.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Expense Approval', $adminName); ?>

            <div class="container">
                <?php show_alert(); ?>

                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <h3>Expense Approval</h3>
                        <a href="AdminClaims.php" class="btn btn-primary" style="text-decoration:none;">
                            + Add New Claims
                        </a>
                    </div>

                    <form class="searchbar" id="searchForm" method="get" action="AdminExpenseApproval.php">
                        <div style="flex: 1; display: flex; gap: 15px; align-items: flex-end;">
                            <div style="flex: 2;">
                                <label style="margin-top: 0;">Search claims:</label>
                                <input type="text" id="tableSearch" name="search" placeholder="Search by employee, department, category..."
                                    value="<?= htmlspecialchars($search) ?>"
                                    oninput="liveSearch()">
                            </div>

                            <div style="flex: 1;">
                                <label style="margin-top: 0;">Filter by Status:</label>
                                <select id="statusFilter" name="statusFilter" onchange="document.getElementById('searchForm').submit();">
                                    <option value="">-- All Statuses --</option>
                                    <?php
                                    while ($rowStatus = mysqli_fetch_assoc($queryStatus)) {
                                        $statusVal = $rowStatus['status'];
                                        $selected = ($statusVal == $selectedStatus) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($statusVal) . "' $selected>" . htmlspecialchars($statusVal) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <a class="btn btn-secondary" href="AdminExpenseApproval.php" style="text-decoration: none; margin-top: auto;">Reset</a>
                    </form>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Claim ID</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($claim = mysqli_fetch_assoc($claims)) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($claim['ClaimID']) ?></td>
                                        <td><?= htmlspecialchars($claim['Name']) ?></td>
                                        <td><?= htmlspecialchars($claim['DepartmentName']) ?></td>
                                        <td><?= htmlspecialchars($claim['CategoryName']) ?></td>
                                        <td><?= htmlspecialchars($claim['Description']) ?></td>
                                        <td><?= money($claim['Amount']) ?></td>
                                        <td><?= date('Y-m-d', strtotime($claim['ClaimDate'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= strtolower($claim['Status']) ?>">
                                                <?= htmlspecialchars($claim['Status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($claim['Status'] === 'Pending') : ?>
                                                <form method="post" action="AdminApprovalProcess.php" style="display:inline; margin: 0;">
                                                    <input type="hidden" name="ClaimID" value="<?= $claim['ClaimID'] ?>">
                                                    <button type="submit" name="approve" value="1" class="btn btn-success">Approve</button>
                                                    <button type="submit" name="reject" value="1" class="btn btn-danger" style="margin-left: 4px;">Reject</button>
                                                </form>
                                            <?php elseif ($claim['Status'] === 'Approved' || $claim['Status'] === 'Rejected') : ?>
                                                <span style="color: #888;">-</span>
                                            <?php endif; ?>
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
</body>

</html>