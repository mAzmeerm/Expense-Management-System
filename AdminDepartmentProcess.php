<?php
session_start();
include("dbconn.php");
include("function.php");
require_login();
$loggedInUser = $_SESSION['UserID'];
$sqlAdmin = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$queryAdmin = mysqli_query($dbconn, $sqlAdmin) or die("Error: " . mysqli_error($dbconn));
if ($adminRow = mysqli_fetch_assoc($queryAdmin)) {
    $adminName = $adminRow['Name'];
} else {
    $adminName = "Admin";
}

$titlePage = "Add Department";
$DepartmentID = "";
$isEditMode = false;
$row = ['DepartmentName' => ''];

//delete action
if (isset($_GET['action']) && ($_GET['action'] === 'deactivate' || $_GET['action'] === 'activate')) {
    $action = $_GET['action'];
    $departmentID = isset($_GET['id']) ? mysqli_real_escape_string($dbconn, $_GET['id']) : '';

    if (!empty($departmentID)) { // FIXED: Changed $employeeID to $departmentID
        $sql = "";

        if ($action === 'deactivate') {
            $checkEmployeesSql = "SELECT EmployeeID FROM employee WHERE DepartmentID = '$departmentID' LIMIT 1";
            $employeeResult = mysqli_query($dbconn, $checkEmployeesSql);

            if ($employeeResult && mysqli_num_rows($employeeResult) > 0) {
                set_alert('error', '<span class="menu-item-wrapper" style="display: inline-flex; align-items: center; width: 100%; box-sizing: border-box; white-space: normal;"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 10px;"> This department cannot be discontinued while employees are assigned to it.</span>', 'AdminDepartmentManagement.php');
                exit(); // FIXED: Added exit() to stop execution
            }

            $sql = "UPDATE department SET Status = 'Discontinued' WHERE DepartmentID = '$departmentID'";
            mysqli_query($dbconn, $sql) or die("Error changing department status: " . mysqli_error($dbconn));

            set_alert('error', '<span class="menu-item-wrapper" style="display: inline-flex; align-items: center; width: 100%; box-sizing: border-box; white-space: normal;"><img src="IconSuccessRed.svg" alt="Checkmark" width="20" height="20" style="margin-right: 10px;"> Department has been discontinued.</span>', 'AdminDepartmentManagement.php');
            exit();
        } elseif ($action === 'activate') {
            $sql = "UPDATE department SET Status = 'In Use' WHERE DepartmentID = '$departmentID'";
            mysqli_query($dbconn, $sql) or die("Error changing department status: " . mysqli_error($dbconn));

            set_alert('success', '<span class="menu-item-wrapper" style="display: inline-flex; align-items: center; width: 100%; box-sizing: border-box; white-space: normal;"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 10px;"> Department status changed back to \'In Use\' successfully.</span>', 'AdminDepartmentManagement.php');
            exit();
        }
    }
}
//edit action
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $DepartmentID = mysqli_real_escape_string($dbconn, $_GET['id']);
    $isEditMode = true;
    $titlePage = "Edit Category";

    $sqlFetch = "SELECT * FROM department WHERE DepartmentID = '$DepartmentID'";
    $result = mysqli_query($dbconn, $sqlFetch) or die("Error fetching category: " . mysqli_error($dbconn));
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        die("Error: Department ID $DepartmentID was not found in the database table.");
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $DepartmentName = mysqli_real_escape_string($dbconn, $_POST['DepartmentName']);

    if ($isEditMode) {
        $sqlUpdate = "UPDATE department SET DepartmentName = '$DepartmentName' WHERE DepartmentID = '$DepartmentID'";
        if (mysqli_query($dbconn, $sqlUpdate)) {
            set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Department updated successfully.</span>', 'AdminDepartmentManagement.php');
        } else {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error updating department: ' . mysqli_error($dbconn) . '</span>', 'AdminDepartmentManagement.php');
        }
    } else {
        $sqlCheck = "SELECT DepartmentID FROM department WHERE DepartmentName = '$DepartmentName'";
        $checkResult = mysqli_query($dbconn, $sqlCheck);

        if (mysqli_num_rows($checkResult) > 0) {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Department "' . htmlspecialchars($DepartmentName) . '" already exists!</span>', 'AdminDepartmentProcess.php');
        } else {
            $sqlInsert = "INSERT INTO department (DepartmentName) VALUES ('$DepartmentName')";
            if (mysqli_query($dbconn, $sqlInsert)) {
                set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Department added successfully.</span>', 'AdminDepartmentManagement.php');
            } else {
                set_alert('error', 'Error adding department: ' . mysqli_error($dbconn), 'AdminDepartmentManagement.php');
            }
        }
    }
}
?>

<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title><?= $titlePage ?></title>
</head>

<body>
    <div class="layout">
        <?php
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header($titlePage, $adminName); ?>

            <div class="mn-content">
                <div class="container">
                    <?php show_alert(); ?>
                    <div class="card">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3><?= $titlePage ?></h3>
                            <a href="AdminDepartmentManagement.php" class="btn btn-secondary"
                                style="text-decoration:none;">
                                Back
                            </a>
                        </div>

                        <?php if (!$isEditMode) { ?>
                            <form method="post" action="AdminDepartmentProcess.php?action=add">
                                <label for="DepartmentName">Department Name:</label>
                                <input type="text" id="DepartmentName" name="DepartmentName"
                                    placeholder="e.g. Management,Marketing etc." required>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Add
                                    Department</button>
                            </form>
                        <?php } else { ?>
                            <form method="post" action="AdminDepartmentProcess.php?action=edit&id=<?= $DepartmentID ?>">
                                <label for="DepartmentName">Department Name:</label>
                                <input type="text" id="DepartmentName" name="DepartmentName"
                                    value="<?= htmlspecialchars($row['DepartmentName']) ?>" required>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Update
                                    Department</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>