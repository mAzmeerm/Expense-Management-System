<?php
session_start();
include("dbconn.php");
include("function.php");
require_login();
$loggedInUser = $_SESSION['UserID'];

$sql = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$query = mysqli_query($dbconn, $sql) or die("Error: " . mysqli_error($dbconn));


if ($row = mysqli_fetch_assoc($query)) {
    $employeeName = $row['Name'];
} else {
    $employeeName = "Employee";
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($dbconn, $_GET['search']) : '';
$selectedStatus = isset($_GET['statusFilter']) ? mysqli_real_escape_string($dbconn, $_GET['statusFilter']) : '';

// PAGINATION 
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1; // Prevent negative page numbers
$offset = ($page - 1) * $limit;
$sqlCount = "SELECT COUNT(*) as total FROM expenseclaim c 
             JOIN expensecategory cat ON c.CategoryID = cat.CategoryID 
             WHERE c.EmployeeID = '$loggedInUser' 
             AND (cat.CategoryName LIKE '%$search%' OR c.Description LIKE '%$search%')";

if ($selectedStatus !== '') {
    $sqlCount .= " AND c.Status = '$selectedStatus'";
}

$countResult = mysqli_query($dbconn, $sqlCount) or die("Count Error: " . mysqli_error($dbconn));
$countRow = mysqli_fetch_assoc($countResult);
$totalRows = $countRow['total'];
$totalPages = ceil($totalRows / $limit);
if ($totalPages < 1) $totalPages = 1;


$sqlClaims = "SELECT c.ClaimID, c.Description, cat.CategoryName, c.Amount, c.Status AS Status, c.Status AS status, c.ClaimDate
              FROM expenseclaim c
              JOIN expensecategory cat ON c.CategoryID = cat.CategoryID
              WHERE c.EmployeeID = '$loggedInUser'
              AND (cat.CategoryName LIKE '%$search%' OR c.Description LIKE '%$search%')";

if ($selectedStatus !== '') {
    $sqlClaims .= " AND c.Status = '$selectedStatus'";
}

$sqlClaims .= " ORDER BY c.ClaimDate DESC LIMIT $offset, $limit";
$claims = mysqli_query($dbconn, $sqlClaims) or die("Claims Fetch Error: " . mysqli_error($dbconn));


$sqlStatus = "SELECT DISTINCT Status AS Status, Status AS status FROM expenseclaim ORDER BY Status ASC";
$queryStatus = mysqli_query($dbconn, $sqlStatus) or die("Error fetch status: " . mysqli_error($dbconn));
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
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



                    <form class="searchbar" id="searchForm" method="get" action="EmployeeMyclaim.php">
                        <div style="flex: 1; display: flex; gap: 15px; align-items: flex-end;">
                            <div style="flex: 2;">
                                <label style="margin-top: 0;">Search claims:</label>
                                <input type="text" id="tableSearch" name="search"
                                    placeholder="Search by category,descriptions..."
                                    value="<?= htmlspecialchars($search) ?>" oninput="liveSearch()">
                            </div>

                            <div style="flex: 1;">
                                <label style="margin-top: 0;">Filter by Status:</label>
                                <select id="statusFilter" name="statusFilter"
                                    onchange="document.getElementById('searchForm').submit();">
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
                        <a class="btn btn-secondary" href="EmployeeMyclaim.php"
                            style="text-decoration: none; margin-top: auto;">Reset</a>
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
                    <?php show_pagination($page, $totalPages, $search, 'statusFilter', $selectedStatus); ?>
                </div>

            </div>
        </div>
    </div>
</body>

</html>