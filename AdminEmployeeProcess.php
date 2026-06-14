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

$isEditMode = false;
$titlePage = "Add Employee";
$employee = [];
$employeeID = "";

/*employee delete */
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['EmployeeID'])) {
    $employeeID = mysqli_real_escape_string($dbconn, $_GET['EmployeeID']);
    $sqlDelete = "DELETE FROM employee WHERE EmployeeID = '$employeeID'";
    if (mysqli_query($dbconn, $sqlDelete)) {
        set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Employee deleted successfully.</span>', 'AdminEmployeeManagement.php');
        exit();
    } else {
        set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error deleting employee: ' . mysqli_error($dbconn) . '</span>', 'AdminEmployeeManagement.php');
        exit();
    }
}
// ==========================================
// DETECT MODE & CAPTURE DATA UPFRONT
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['EmployeeID'])) {
    $isEditMode = true;
    $titlePage = "Edit Employee";
    $employeeID = mysqli_real_escape_string($dbconn, $_GET['EmployeeID']);

    // Fetch employee data right away so the form can display it
    $sqlFetch = "SELECT * FROM employee WHERE EmployeeID = '$employeeID'";
    $result = mysqli_query($dbconn, $sqlFetch) or die("Query Failed: " . mysqli_error($dbconn));
    $employee = mysqli_fetch_assoc($result);

    if (!$employee) {
        die("Error: Employee ID $employeeID was not found.");
    }
}

// ==========================================
// PROCESS FORM SUBMISSIONS (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($dbconn, $_POST['name']);
    $email = mysqli_real_escape_string($dbconn, $_POST['email']);
    $password = mysqli_real_escape_string($dbconn, $_POST['password']);
    $phone = mysqli_real_escape_string($dbconn, $_POST['phone']);
    $departmentID = mysqli_real_escape_string($dbconn, $_POST['department']);
    $role = mysqli_real_escape_string($dbconn, $_POST['role']);

    if ($isEditMode) {
        // EXECUTE UPDATE TRANSACTION
        $sqlUpdate = "UPDATE employee 
                      SET Name='$name', Email='$email', Password='$password', PhoneNum='$phone', DepartmentID='$departmentID', Role='$role' 
                      WHERE EmployeeID='$employeeID'";

        if (mysqli_query($dbconn, $sqlUpdate)) {
            set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Employee updated successfully.</span>', 'AdminEmployeeManagement.php');
            exit();
        } else {
            set_alert('error', 'Error updating employee: ' . mysqli_error($dbconn), 'AdminEmployeeManagement.php');
            exit();
        }
    } else {
        // EXECUTE INSERT TRANSACTION
        $sqlInsert = "INSERT INTO employee (Name, Email, Password, PhoneNum, DepartmentID, Role) 
                      VALUES ('$name', '$email', '$password', '$phone', '$departmentID', '$role')";

        if (mysqli_query($dbconn, $sqlInsert)) {
            set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Employee added successfully.</span>', 'AdminEmployeeManagement.php');
            exit();
        } else {
            set_alert('error', 'Error adding employee: ' . mysqli_error($dbconn), 'AdminEmployeeManagement.php');
            exit();
        }
    }
}
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title><?php echo $titlePage; ?></title>
</head>

<body>
    <div class="layout">
        <?php include 'AdminSidebar.php'; ?>

        <div class="main-content">
            <?php show_header('Admin Employee Process', $adminName); ?>
            <div class="mn-content">
                <div class="container">
                    <div class="card">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3><?php echo $titlePage; ?></h3>
                            <a href="AdminEmployeeManagement.php" class="btn btn-secondary">Back</a>
                        </div>

                        <?php if (!$isEditMode) { ?>
                            <form method="post" action="AdminEmployeeProcess.php?action=add">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" required>

                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" required>
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" required>
                                <label for="phone">Phone Number:</label>
                                <input type="text" id="phone" name="phone" required>

                                <label for="department">Department:</label>
                                <select id="department" name="department" required>
                                    <option value="">-- Select Department --</option>
                                    <?php
                                    $sqlDepartments = "SELECT DepartmentID, DepartmentName FROM department";
                                    $queryDepartments = mysqli_query($dbconn, $sqlDepartments) or die("Error: " . mysqli_error($dbconn));
                                    while ($row = mysqli_fetch_assoc($queryDepartments)) {
                                        echo "<option value='" . $row['DepartmentID'] . "'>" . htmlspecialchars($row['DepartmentName']) . "</option>";
                                    }
                                    ?>
                                </select>

                                <label for="role">Role:</label>
                                <select id="role" name="role" required>
                                    <option value="Staff">Staff</option>
                                    <option value="Admin">Admin</option>
                                </select>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Add Employee</button>
                            </form>

                        <?php } else { ?>
                            <form method="post" action="AdminEmployeeProcess.php?action=update&EmployeeID=<?php echo $employeeID; ?>">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee['Name']); ?>" required>

                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employee['Email']); ?>" required>

                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($employee['Password']); ?>" required>

                                <label for="phone">Phone Number:</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($employee['PhoneNum']); ?>" required>

                                <label for="department">Department:</label>
                                <select id="department" name="department" required>
                                    <?php
                                    $sqlDepartments = "SELECT DepartmentID, DepartmentName FROM department";
                                    $queryDepartments = mysqli_query($dbconn, $sqlDepartments) or die("Error: " . mysqli_error($dbconn));
                                    while ($row = mysqli_fetch_assoc($queryDepartments)) {
                                        $selected = ($row['DepartmentID'] == $employee['DepartmentID']) ? "selected" : "";
                                        echo "<option value='" . $row['DepartmentID'] . "' $selected>" . htmlspecialchars($row['DepartmentName']) . "</option>";
                                    }
                                    ?>
                                </select>

                                <label for="role">Role:</label>
                                <select id="role" name="role" required>
                                    <option value="Staff" <?php echo ($employee['Role'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                                    <option value="Admin" <?php echo ($employee['Role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Update Employee</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>