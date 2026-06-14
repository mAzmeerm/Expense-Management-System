<?php
session_start();
include("dbconn.php");
include("function.php");

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
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $DepartmentID = mysqli_real_escape_string($dbconn, $_GET['id']);

    $sqlDelete = "DELETE FROM department WHERE DepartmentID = '$DepartmentID'";
    if (mysqli_query($dbconn, $sqlDelete)) {
        set_alert('error', '<span class="menu-item-wrapper"><img src="IconSuccessRed.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Department deleted successfully.</span>', 'AdminDepartmentManagement.php');
        exit();
    } else {
        set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error deleting department: ' . mysqli_error($dbconn) . '</span>', 'AdminDepartmentManagement.php');
        exit();
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
        $sqlUpdate = "UPDATE department SET CategoryName = '$DepartmentName' WHERE CategoryID = '$DepartmentID'";
        if (mysqli_query($dbconn, $sqlUpdate)) {
            set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Department updated successfully.</span>', 'AdminDepartmentManagement.php');
        } else {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error updating department: ' . mysqli_error($dbconn) . '</span>', 'AdminDepartmentManagement.php');
        }
    } else {
        $sqlCheck = "SELECT DepartmentID FROM department WHERE DepartmentName = '$DepartmentName'";
        $checkResult = mysqli_query($dbconn, $sqlCheck);

        if (mysqli_num_rows($checkResult) > 0) {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Category "' . htmlspecialchars($DepartmentName) . '" already exists!</span>', 'AdminDepartmentManagement.php');
            
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
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3><?= $titlePage ?></h3>
                            <a href="AdminDepartmentManagement.php" class="btn btn-secondary" style="text-decoration:none;">
                                Back
                            </a>
                        </div>

                        <?php if (!$isEditMode) { ?>
                            <form method="post" action="AdminDepartmentProcess.php?action=add">
                                <label for="DepartmentName">Department Name:</label>
                                <input type="text" id="DepartmentName" name="DepartmentName" placeholder="e.g. Management,Marketing etc." required>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Add Department</button>
                            </form>
                        <?php } else { ?>
                            <form method="post" action="AdminDepartmentProcess.php?action=edit&id=<?= $DepartmentID ?>">
                                <label for="DepartmentName">Department Name:</label>
                                <input type="text" id="DepartmentName" name="DepartmentName" value="<?= htmlspecialchars($row['DepartmentName']) ?>" required>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Update Department</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>