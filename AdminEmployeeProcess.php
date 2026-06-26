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

$isEditMode = false;
$titlePage = "Add Employee";
$employee = [];
$employeeID = "";
if (isset($_GET['EmployeeID'])) {
    $employeeID = mysqli_real_escape_string($dbconn, $_GET['EmployeeID']);
} elseif (isset($_POST['EmployeeID'])) {
    $employeeID = mysqli_real_escape_string($dbconn, $_POST['EmployeeID']);
}

//deactive / restore
if (isset($_GET['action']) && ($_GET['action'] === 'deactivate' || $_GET['action'] === 'activate')) {
    $action = $_GET['action'];
    $employeeID = isset($_GET['id']) ? mysqli_real_escape_string($dbconn, $_GET['id']) : '';
    if (!empty($employeeID)) {
        $sql = "";

        if ($action === 'deactivate') {
            $checkRoleSql = "SELECT Role FROM employee WHERE EmployeeID = '$employeeID' AND Role = 'Admin'";
            $roleResult = mysqli_query($dbconn, $checkRoleSql);
            if ($roleResult && mysqli_num_rows($roleResult) > 0) {
                set_alert('error', '<span class="menu-item-wrapper" style="display: inline-flex; align-items: center; width: 100%; box-sizing: border-box; white-space: normal;"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 10px;"> Cannot deactivate an admin account!</span>', 'AdminEmployeeManagement.php');
            }
            $sql = "UPDATE employee SET Status = 'Inactive' WHERE EmployeeID = '$employeeID'";

            // Execute query FIRST before setting the alert and redirecting
            mysqli_query($dbconn, $sql) or die("Error changing profile status: " . mysqli_error($dbconn));

            set_alert('error', '<span class="menu-item-wrapper" style="display: inline-flex; align-items: center; width: 100%; box-sizing: border-box; white-space: normal;"><img src="IconSuccessRed.svg" alt="Checkmark" width="20" height="20" style="margin-right: 10px;"> Employee has been deactivated </span>', 'AdminEmployeeManagement.php');
        } elseif ($action === 'activate') {
            $sql = "UPDATE employee SET Status = 'Active' WHERE EmployeeID = '$employeeID'";

            // Execute query FIRST before setting the alert and redirecting
            mysqli_query($dbconn, $sql) or die("Error changing profile status: " . mysqli_error($dbconn));

            // FIX: Changed text to Activated and matched your styling fixes from earlier
            set_alert('success', '<span class="menu-item-wrapper" style="display: inline-flex; align-items: center; width: 100%; box-sizing: border-box; white-space: normal;"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 10px; "> Change employee status back to active successfully.</span>', 'AdminEmployeeManagement.php');
        }
    }
}

// Set Edit Mode condition if action is update OR if we have a valid ID during a form submission
if ((isset($_GET['action']) && $_GET['action'] === 'update') || isset($_POST['EmployeeID']) && $_POST['EmployeeID'] !== "") {
    $isEditMode = true;
    $titlePage = "Edit Employee";

    // Fetch employee data right away so the form can display it
    $sqlFetch = "SELECT * FROM employee WHERE EmployeeID = '$employeeID'";
    $result = mysqli_query($dbconn, $sqlFetch) or die("Query Failed: " . mysqli_error($dbconn));
    $employee = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($dbconn, $_POST['name']);
    $email = mysqli_real_escape_string($dbconn, $_POST['email']);
    $phone = mysqli_real_escape_string($dbconn, $_POST['phone']);
    $departmentID = mysqli_real_escape_string($dbconn, $_POST['department']);
    $role = mysqli_real_escape_string($dbconn, $_POST['role']);

    if ($isEditMode) {
        $sqlCheckUpdate = "SELECT Email FROM employee WHERE Email = '$email' AND EmployeeID != '$employeeID'";
        $checkResultUpdate = mysqli_query($dbconn, $sqlCheckUpdate);

        if (mysqli_num_rows($checkResultUpdate) > 0) {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;">Email already exists!</span>', 'AdminEmployeeProcess.php?action=update&EmployeeID=' . $employeeID);
        } else {
            $sqlUpdate = "UPDATE employee 
                          SET Name='$name', Email='$email', PhoneNum='$phone', DepartmentID='$departmentID', Role='$role'";
            if (!empty($_POST['password'])) {
                $hashedPassword = mysqli_real_escape_string($dbconn, hash_password($_POST['password']));
                $sqlUpdate .= ", Password='$hashedPassword'";
            }
            $sqlUpdate .= " WHERE EmployeeID='$employeeID'";

            if (mysqli_query($dbconn, $sqlUpdate)) {
                set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Employee updated successfully.</span>', 'AdminEmployeeManagement.php');
            } else {
                set_alert('error', 'Error updating employee: ' . mysqli_error($dbconn), 'AdminEmployeeManagement.php');
            }
        }
    } else {
        $sqlCheck = "SELECT Email FROM employee WHERE Email = '$email'";
        $checkResult = mysqli_query($dbconn, $sqlCheck);

        if (mysqli_num_rows($checkResult) > 0) {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;">Email already exists!</span>', 'AdminEmployeeProcess.php');
        } else {
            $hashedPassword = mysqli_real_escape_string($dbconn, hash_password($_POST['password']));
            $sqlInsert = "INSERT INTO employee (Name, Email, Password, PhoneNum, DepartmentID, Role) 
                          VALUES ('$name', '$email', '$hashedPassword', '$phone', '$departmentID', '$role')";

            if (mysqli_query($dbconn, $sqlInsert)) {
                set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Employee added successfully.</span>', 'AdminEmployeeManagement.php');
            } else {
                set_alert('error', 'Error adding employee: ' . mysqli_error($dbconn), 'AdminEmployeeManagement.php');
            }
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
                    <?php show_alert(); ?>
                    <div class="card">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
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
                                <label
                                    style="display: block; font-weight: normal; font-size: 14px; color: #334155; cursor: pointer; user-select: none;">
                                    <input type="checkbox"
                                        onclick="document.getElementById('password').type = this.checked ? 'text' : 'password'"
                                        style="width: 16px; height: 16px; margin: 0 8px 0 0; vertical-align: middle; cursor: pointer;">
                                    <span style="vertical-align: middle;">Show Password</span>
                                </label>
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
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Add
                                    Employee</button>
                            </form>

                        <?php } else { ?>
                            <form method="post" action="AdminEmployeeProcess.php">
                                <input type="hidden" name="EmployeeID" value="<?= htmlspecialchars($employeeID) ?>">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name"
                                    value="<?php echo htmlspecialchars($employee['Name']); ?>" required>

                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email"
                                    value="<?php echo htmlspecialchars($employee['Email']); ?>" required>

                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password"
                                    placeholder="Leave blank to keep current password">

                                <label for="phone">Phone Number:</label>
                                <input type="text" id="phone" name="phone"
                                    value="<?php echo htmlspecialchars($employee['PhoneNum']); ?>" required>

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
                                    <option value="Staff" <?php echo ($employee['Role'] == 'Staff') ? 'selected' : ''; ?>>
                                        Staff</option>
                                    <option value="Admin" <?php echo ($employee['Role'] == 'Admin') ? 'selected' : ''; ?>>
                                        Admin</option>
                                </select>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Update
                                    Employee</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>