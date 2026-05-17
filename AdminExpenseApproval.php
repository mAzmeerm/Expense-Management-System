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
$sql2 = "select * from expenseclaim";
$query = mysqli_query($dbconn, $sql2) or die("Error: " . mysqli_error($dbconn));
$row = mysqli_num_rows($query);

// 2. Process search keywords securely
$search = isset($_GET['search']) ? mysqli_real_escape_string($dbconn, $_GET['search']) : '';

// 3. Fetch all matching Pending claims
$sqlClaims = "SELECT c.*, e.Name, cat.CategoryName, d.DepartmentName
              FROM expenseclaim c 
              JOIN employee e ON c.EmployeeID = e.EmployeeID 
              JOIN expensecategory cat ON c.CategoryID = cat.CategoryID 
              JOIN department d ON e.DepartmentID = d.DepartmentID
              WHERE (e.Name LIKE '%$search%' 
                   OR cat.CategoryName LIKE '%$search%' 
                   OR c.Status LIKE '%$search%' 
                   OR d.DepartmentName LIKE '%$search%') 
              ORDER BY c.ClaimDate DESC";

$claims = mysqli_query($dbconn, $sqlClaims) or die("Error: " . mysqli_error($dbconn));
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Expense Approval</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminExpenseApproval.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <header>
                <strong>Expense Approval</strong>
                <span>Welcome, <?php echo $adminName; ?></span>
            </header>

            <div class="container">
                <?php if (isset($_SESSION['approval_message'])): ?>

                    <?php echo $_SESSION['approval_message']; ?>

                    <?php unset($_SESSION['approval_message']); ?>
                <?php endif; ?>
                <div class="card">
                    <form class="searchbar" method="get" style="display: flex; align-items: flex-end; gap: 0.5rem; margin-bottom: 1.5rem;">
                        <div style="flex: 1;">
                            <label>Search claims:</label>
                            <input type="text" name="search" placeholder="Search by employee, category, or status" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </div>
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a class="btn btn-secondary" href="AdminExpenseApproval.php" style="text-decoration: none;">Reset</a>
                    </form>

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
                            <?php
                            while ($claim = mysqli_fetch_assoc($claims)) {
                            ?>
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
                                        <?php if ($claim['Status'] === 'Pending') { ?>
                                            <form method="post" action="approvalProcess.php" style="display:inline">
                                                <input type="hidden" name="ClaimID" value="<?= $claim['ClaimID'] ?>">
                                                <button type="submit" name="approve" value="1" class="btn btn-success">Approve</button>
                                                <button type="submit" name="reject" value="1" class="btn btn-danger">Reject</button>
                                            </form>
                                        <?php } ?>
                                    </td>
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