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

//Fetch all matching departments
$sqlDepartment = "SELECT *
                  FROM department 
                  WHERE DepartmentName LIKE '%$search%' 
                  ORDER BY DepartmentID ASC";

$departments = mysqli_query($dbconn, $sqlDepartment) or die("Error processing department query: " . mysqli_error($dbconn));
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <title>Admin Department Management</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminDepartmentManagement.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Department Management', $adminName); ?>

            <div class="mn-content">
                <div class="container">
                    <?= show_alert(); ?>
                    <div class="card">

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>Department Management</h3>
                            <a href="AdminDepartmentProcess.php?action=add" class="btn btn-primary" style="text-decoration:none;">
                                + Add New Department
                            </a>
                        </div>

                        <form class="searchbar" id="searchForm" method="get" onsubmit="event.preventDefault();">
                            <div style="flex: 1; display: flex; gap: 15px; align-items: flex-end;">
                                <div style="flex: 2;">
                                    <label style="margin-top: 0;">Search Department name:</label>
                                    <input type="text" id="tableSearch" name="search"
                                        placeholder="Search by department name" value="<?= htmlspecialchars($search) ?>"
                                        oninput="liveSearch()">
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('tableSearch').value=''; liveSearch();" style="margin-top: auto;">Reset</button>
                        </form>

                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Department ID</th>
                                        <th>Department Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($departments) > 0) {
                                        while ($row = mysqli_fetch_assoc($departments)) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['DepartmentID']); ?></td>
                                                <td><?php echo htmlspecialchars($row['DepartmentName']); ?></td>
                                                <td>
                                                    <?php
                        
                                                    $badgeClass = (trim(strtolower($row['Status'])) === 'in use') ? 'active' : 'inactive';
                                                    ?>
                                                    <span class="badge badge-<?= $badgeClass ?>">
                                                        <?= htmlspecialchars($row['Status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="AdminDepartmentProcess.php?action=edit&id=<?php echo $row['DepartmentID']; ?>" class="btn btn-secondary">Edit</a>
                                                    <?php if ($row['Status'] == 'In Use'): ?>
                                                        <a href="AdminDepartmentProcess.php?action=deactivate&id=<?= $row['DepartmentID'] ?>"
                                                            class="btn btn-danger"
                                                            onclick="return confirm('Are you sure you want to Discontinued the department?');">
                                                            Discontinue
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="AdminDepartmentProcess.php?action=activate&id=<?= $row['DepartmentID'] ?>"
                                                            class="btn btn-success"
                                                            onclick="return confirm('Do you want to restore this department back?');">
                                                            Restore
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="3" style="text-align:center;">No departments found.</td>
                                        </tr>
                                    <?php } ?>
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