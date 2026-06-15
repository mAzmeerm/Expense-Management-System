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

$titlePage = "Add Category";
$categoryID = "";
$isEditMode = false;
$row = ['CategoryName' => ''];

//delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $categoryID = mysqli_real_escape_string($dbconn, $_GET['id']);

    $sqlDelete = "DELETE FROM expensecategory WHERE CategoryID = '$categoryID'";
    if (mysqli_query($dbconn, $sqlDelete)) {
        set_alert('error', '<span class="menu-item-wrapper"><img src="IconSuccessRed.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Category deleted successfully.</span>', 'AdminCategoryManagement.php');
        exit();
    } else {
        set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error deleting category: ' . mysqli_error($dbconn) . '</span>', 'AdminCategoryManagement.php');
        exit();
    }
}

//edit action
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $categoryID = mysqli_real_escape_string($dbconn, $_GET['id']);
    $isEditMode = true;
    $titlePage = "Edit Category";

    $sqlFetch = "SELECT * FROM expensecategory WHERE CategoryID = '$categoryID'";
    $result = mysqli_query($dbconn, $sqlFetch) or die("Error fetching category: " . mysqli_error($dbconn));
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        die("Error: Category ID $categoryID was not found in the database table.");
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = mysqli_real_escape_string($dbconn, $_POST['CategoryName']);

    if ($isEditMode) {
        $sqlUpdate = "UPDATE expensecategory SET CategoryName = '$categoryName' WHERE CategoryID = '$categoryID'";
        if (mysqli_query($dbconn, $sqlUpdate)) {
            set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Category updated successfully.</span>', 'AdminCategoryManagement.php');
            exit();
        } else {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Error updating category: ' . mysqli_error($dbconn) . '</span>', 'AdminCategoryManagement.php');
            exit();
        }
    } else {
        $sqlCheck = "SELECT CategoryID FROM expensecategory WHERE CategoryName = '$categoryName'";
        $checkResult = mysqli_query($dbconn, $sqlCheck);

        if (mysqli_num_rows($checkResult) > 0) {
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Category "' . htmlspecialchars($categoryName) . '" already exists!</span>', 'AdminCategoryManagement.php');
            exit();
        } else {
            $sqlInsert = "INSERT INTO expensecategory (CategoryName) VALUES ('$categoryName')";
            if (mysqli_query($dbconn, $sqlInsert)) {
                set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Category added successfully.</span>', 'AdminCategoryManagement.php');
                exit();
            } else {
                set_alert('error', 'Error adding category: ' . mysqli_error($dbconn), 'AdminCategoryManagement.php');
                exit();
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
        $activePage = 'AdminCategoryManagement.php';
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
                            <a href="AdminCategoryManagement.php" class="btn btn-secondary" style="text-decoration:none;">
                                Back
                            </a>
                        </div>

                        <?php if (!$isEditMode) { ?>
                            <form method="post" action="AdminCategoryProcess.php?action=add">
                                <label for="CategoryName">Category Name:</label>
                                <input type="text" id="CategoryName" name="CategoryName" placeholder="e.g. Travel, Food, etc." required>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Add Category</button>
                            </form>
                        <?php } else { ?>
                            <form method="post" action="AdminCategoryProcess.php?action=edit&id=<?= $categoryID ?>">
                                <label for="CategoryName">Category Name:</label>
                                <input type="text" id="CategoryName" name="CategoryName" value="<?= htmlspecialchars($row['CategoryName']) ?>" required>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Update Category</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>