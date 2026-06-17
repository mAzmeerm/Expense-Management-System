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

$selectedDepartment = isset($_GET['DepartmentFilter']) ? mysqli_real_escape_string($dbconn, $_GET['DepartmentFilter']) : '';

$sqlEmployees = "SELECT e.*, d.DepartmentName
                 FROM employee e 
                 JOIN department d ON e.DepartmentID = d.DepartmentID
                 WHERE (e.Name LIKE '%$search%' 
                    OR e.Email LIKE '%$search%' 
                    OR d.DepartmentName LIKE '%$search%')";

if ($selectedDepartment !== '') {
    $sqlEmployees .= " AND d.DepartmentName = '$selectedDepartment'";
}

$sqlEmployees .= " ORDER BY d.DepartmentName DESC";
$employees = mysqli_query($dbconn, $sqlEmployees) or die("Error processing employees query: " . mysqli_error($dbconn));
$sqlDepartment = "SELECT DISTINCT DepartmentName FROM department ORDER BY DepartmentName ASC";
$queryDepartment = mysqli_query($dbconn, $sqlDepartment) or die("Error fetching departments: " . mysqli_error($dbconn));
?>

<html>

<head>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer> </script>
    <title>Admin Employee Management</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminEmployeeManagement.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Employee Management', $adminName); ?>

            <div class="mn-content">
                <div class="container">
                    <?php show_alert(); ?>
                    <div class="card">

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>Employee Management</h3>
                            <a href="AdminEmployeeProcess.php?action=add" class="btn btn-primary" style="text-decoration:none;">
                                + Add New Employee
                            </a>
                        </div>

                        <form class="searchbar" id="searchForm" method="get" action="AdminEmployeeManagement.php">
                            <div style="flex: 1; display: flex; gap: 15px; align-items: flex-end;">

                                <div style="flex: 2;">
                                    <label style="margin-top: 0;">Search Employees:</label>
                                    <input type="text" id="tableSearch" name="search" placeholder="Search by name, email..."
                                        value="<?= htmlspecialchars($search) ?>"
                                        oninput="liveSearch()">
                                </div>

                                <div style="flex: 1;">
                                    <label style="margin-top: 0;">Filter by Department:</label>
                                    <select id="DepartmentFilter" name="DepartmentFilter" onchange="this.form.submit();">
                                        <option value="">-- All Departments --</option>
                                        <?php
                                        while ($rowDepartment = mysqli_fetch_assoc($queryDepartment)) {
                                            $DepartmentVal = $rowDepartment['DepartmentName'];
                                            $selected = ($DepartmentVal == $selectedDepartment) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($DepartmentVal) . "' $selected>" . htmlspecialchars($DepartmentVal) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <a class="btn btn-secondary" href="AdminEmployeeManagement.php" style="text-decoration: none; margin-top: auto;">Reset</a>
                        </form>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($employee = mysqli_fetch_assoc($employees)) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($employee['EmployeeID']) ?></td>
                                            <td><?= htmlspecialchars($employee['Name']) ?></td>
                                            <td><?= htmlspecialchars($employee['DepartmentName']) ?></td>
                                            <td><?= htmlspecialchars($employee['Email']) ?></td>
                                            <td><?= htmlspecialchars($employee['PhoneNum']) ?></td>
                                            <td><?= htmlspecialchars($employee['Role']) ?></td>
                                            <td>
                                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                                    <a href="AdminEmployeeProcess.php?action=update&EmployeeID=<?= $employee['EmployeeID'] ?>"
                                                        class="btn btn-secondary" style="text-decoration: none;">Edit</a>
                                                    <a href="AdminEmployeeProcess.php?action=delete&EmployeeID=<?= $employee['EmployeeID'] ?>"
                                                        class="btn btn-danger" style="text-decoration: none;" onclick="return confirm('Are you sure you want to delete this employee?');">
                                                        Delete
                                                    </a>
                                                </div>
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