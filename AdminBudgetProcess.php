<?php
session_start();
include("dbconn.php");
include("function.php");
require_login();
$loggedInUser = $_SESSION['UserID'];
$sqlAdmin = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$queryAdmin = mysqli_query($dbconn, $sqlAdmin) or die("Error: "
    . mysqli_error($dbconn));
if ($row = mysqli_fetch_assoc($queryAdmin)) {
    $adminName = $row['Name'];
} else {
    $adminName = "Admin";
}


$isEditMode = false;
$budgetData = [];
$pageTitle = "Admin Add New Budget";

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['BudgetID'])) {
    $isEditMode = true;
    $budgetID = (int)$_GET['BudgetID'];
    $pageTitle = "Edit Budget";

    $sqlFetch = "SELECT b.*, d.DepartmentName FROM budget b 
                 JOIN department d ON b.DepartmentID = d.DepartmentID 
                 WHERE b.BudgetID = $budgetID";

    $result = mysqli_query($dbconn, $sqlFetch) or die("Query Failed: " . mysqli_error($dbconn));
    $budgetData = mysqli_fetch_assoc($result);

    if (!$budgetData) {
        die("Error: Budget ID $budgetID was not found in the database table.");
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isEditMode) {
        $spentAmount = $budgetData['SpentAmount'];
        $allocatedAmount = mysqli_real_escape_string($dbconn, $_POST['Amount']);
        $description = mysqli_real_escape_string($dbconn, $_POST['Description']);
        if ($allocatedAmount < $spentAmount) {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Allocated amount must be greater than or equal to the spent amount.</span>', 'AdminBudgetManagement.php');
        }
        $sqlUpdate = "UPDATE budget SET AllocatedAmount = '$allocatedAmount', Description = '$description' WHERE BudgetID = $budgetID";
        if (mysqli_query($dbconn, $sqlUpdate)) {
            set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Budget updated successfully.</span>', 'AdminBudgetManagement.php');
        } else {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error updating budget: ' . mysqli_error($dbconn) . '</span>', 'AdminBudgetManagement.php');
        }
    }
}
else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $year = mysqli_real_escape_string($dbconn, $_POST['Year']);
        $departmentID = mysqli_real_escape_string($dbconn, $_POST['DepartmentID']);
        $allocatedAmount = mysqli_real_escape_string($dbconn, $_POST['Amount']);
        $description = mysqli_real_escape_string($dbconn, $_POST['Description']);

        $sqlInsert = "INSERT INTO budget (DepartmentID, Year, AllocatedAmount, SpentAmount, RemainAmount, Description) 
                      VALUES ('$departmentID', '$year', '$allocatedAmount', 0, '$allocatedAmount', '$description')";
        
        if (mysqli_query($dbconn, $sqlInsert)) {
            set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Budget created successfully.</span>', 'AdminBudgetManagement.php');
        } else {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error creating budget: ' . mysqli_error($dbconn) . '</span>', 'AdminBudgetManagement.php');
        }
    }
}

?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title> <?= $pageTitle ?> </title>
</head>

<body>
    <div class="layout">
        <?php include 'AdminSidebar.php'; ?>

        <div class="main-content">
            <?php show_header($pageTitle, $adminName); ?>

            <div class="container">
                <div class="card">

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <h3><?= htmlspecialchars($pageTitle) ?></h3>
                        <a href="AdminBudgetManagement.php" class="btn btn-secondary" style="text-decoration:none;">Back</a>
                    </div>

                    <form method="post" action="">

                        <?php if ($isEditMode): ?>

                            <label style="color: #666; font-size: 0.9rem;">Department & Year:</label>
                            <input type="text" value="<?= htmlspecialchars($budgetData['DepartmentName']) ?> (<?= htmlspecialchars($budgetData['Year']) ?>)" disabled style="background:#f4f4f4; margin-bottom: 15px;">

                        <?php else: ?>

                            <label for="Year">Year:</label>
                            <input type="number" id="Year" name="Year" min="2000" max="2100" placeholder="e.g. 2026" required style="margin-bottom: 15px;">

                            <label for="Department">Department:</label>
                            <select id="Department" name="DepartmentID" required style="margin-bottom: 15px;">
                                <option value="">-- Select Department --</option>
                                <?php
                                $queryDept = mysqli_query($dbconn, "SELECT DepartmentID, DepartmentName FROM department");
                                while ($rowDept = mysqli_fetch_assoc($queryDept)) {
                                    echo "<option value='" . $rowDept['DepartmentID'] . "'>" . $rowDept['DepartmentName'] . "</option>";
                                }
                                ?>
                            </select>

                        <?php endif; ?>
                        <label for="Amount">Allocated Amount (RM):</label>
                        <input type="number" id="Amount" name="Amount" value="<?= $isEditMode ? htmlspecialchars($budgetData['AllocatedAmount']) : '' ?>" step="0.01" required style="margin-bottom: 15px;">

                        <label for="Description">Description:</label>
                        <textarea id="Description" name="Description" required><?= $isEditMode ? htmlspecialchars($budgetData['Description']) : '' ?></textarea>

                        <button type="submit" class="btn btn-primary" style="margin-top: 15px; width: 100%;">
                            <?= $isEditMode ? 'Save Changes' : 'Create Budget' ?>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>

</html>