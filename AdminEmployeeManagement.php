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

// 2. Process search keywords securely
$search = isset($_GET['search']) ? mysqli_real_escape_string($dbconn, $_GET['search']) : '';

// 3. Fetch all matching employees
$sqlEmployees = "SELECT e.*, d.DepartmentName
                 FROM employee e 
                 JOIN department d ON e.DepartmentID = d.DepartmentID
                 WHERE e.Name LIKE '%$search%' 
                    OR e.Email LIKE '%$search%' 
                    OR d.DepartmentName LIKE '%$search%'
                 ORDER BY e.Name ASC";
$employees = mysqli_query($dbconn, $sqlEmployees) or die("Error: " . mysqli_error($dbconn));
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="style.css?v=1.1">
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
                    <div class="card">

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>Employee Management</h3>
                            <a href="AdminAddEmployee.php" class="btn btn-primary" style="text-decoration:none;">
                                + Add New Employee
                            </a>
                        </div>

                        <form class="searchbar" method="get">
                            <div style="flex: 1;">
                                <label style="margin-top: 0;">Search employee:</label>
                                <input type="text" name="search" placeholder="Name, email, or department"
                                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            </div>
                            <button class="btn btn-primary" type="submit">Search</button>
                            <a class="btn btn-secondary" href="AdminEmployeeManagement.php" style="text-decoration: none;">Reset</a>
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
                                                    <a href="AdminEditEmployee.php?EmployeeID=<?= $employee['EmployeeID'] ?>"
                                                       class="btn btn-secondary" style="text-decoration: none;">Edit</a>
                                                    
                                                    <form name="EmployeeProcess_<?= $employee['EmployeeID'] ?>" method="post" action="EmployeeProcess.php" style="margin: 0; display: inline;">
                                                        <input type="hidden" name="EmployeeID" value="<?= $employee['EmployeeID'] ?>">
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this employee?');">
                                                            Delete
                                                        </button>
                                                    </form>
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