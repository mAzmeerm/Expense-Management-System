<?php
session_start();
include("dbconn.php");
include("function.php");
$loggedInUser = $_SESSION['UserID'];

if ($loggedInUser == '' or !isset($loggedInUser)) {
    header("Location: login.php");
    exit();
}

// Query to look up this specific employee
$sql = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));

// Fetch the data record
if ($row = mysqli_fetch_assoc($query)) {
    $employeeName = $row['Name'];
} else {
    $employeeName = "Employee";
}

// Search input
$search = isset($_GET['search']) ? mysqli_real_escape_string($dbconn, trim($_GET['search'])) : '';

// Query to fetch this employee's claims, with optional search filter
$sql_claims = "SELECT c.ClaimID, c.Description, cat.CategoryName, c.Amount, c.Status, c.ClaimDate
               FROM expenseclaim c
               JOIN expensecategory cat ON c.CategoryID = cat.CategoryID
               WHERE c.EmployeeID = '$loggedInUser'";

if ($search !== '') {
    $sql_claims .= " AND (c.ClaimID LIKE '%$search%'
                     OR LOWER(c.Description) LIKE LOWER('%$search%')
                     OR LOWER(cat.CategoryName) LIKE LOWER('%$search%'))";
}

$sql_claims .= " ORDER BY c.ClaimDate DESC";
$claims = mysqli_query($dbconn, $sql_claims) or die("Error: " . mysqli_error($dbconn));
?>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>My Claims</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'EmployeeMyClaim.php';
        include 'EmployeeSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('My Claims', $employeeName); ?>

            <div class="container">

                <div class="card">
                    <h3>My Claims</h3>

                    <form method="GET" action="EmployeeMyClaim.php">
                        <label for="search" style="margin-top:1rem;">Search claims:</label>
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1.2rem;">
                            <input type="text" id="search" name="search" placeholder="Claim ID, description or category..." value="<?= htmlspecialchars($search) ?>" style="margin-top:0; flex:1;">
                            <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Search</button>
                            <a href="EmployeeMyClaim.php" class="btn btn-secondary" style="white-space:nowrap;">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Claim ID</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($claims) > 0): ?>
                                    <?php while ($r = mysqli_fetch_assoc($claims)): ?>
                                        <tr>
                                            <td><?= $r['ClaimID'] ?></td>
                                            <td><?= htmlspecialchars($r['Description']) ?></td>
                                            <td><?= $r['CategoryName'] ?></td>
                                            <td><?= money($r['Amount']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= strtolower($r['Status']) ?>">
                                                    <?= $r['Status'] ?>
                                                </span>
                                            </td>
                                            <td><?= date('Y-m-d', strtotime($r['ClaimDate'])) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center; color: var(--color-muted);">No claims found.</td>
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