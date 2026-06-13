<?php
session_start();
include("dbconn.php");
include("function.php");
$loggedInUser = $_SESSION['UserID'];
if ($loggedInUser == '' or !isset($loggedInUser)) {
    header("Location: login.php");
    exit();
}
$sqlAdmin = "SELECT Name FROM employee WHERE EmployeeID = '$loggedInUser'";
$queryAdmin = mysqli_query($dbconn, $sqlAdmin) or die("Error: "
    . mysqli_error($dbconn));
if ($row = mysqli_fetch_assoc($queryAdmin)) {
    $adminName = $row['Name'];
} else {
    $adminName = "Admin";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = mysqli_real_escape_string($dbconn, $_POST['Description']);
    $amount = mysqli_real_escape_string($dbconn, $_POST['Amount']);
    $categoryID = mysqli_real_escape_string($dbconn, $_POST['Category']);
    $claimDate = mysqli_real_escape_string($dbconn, $_POST['ClaimDate']);

    $sqlInsert = "INSERT INTO expenseclaim (EmployeeID, Description, Amount, CategoryID, ClaimDate, Status) 
                  VALUES ('$loggedInUser', '$description', '$amount', '$categoryID', '$claimDate', 'Pending')";
                   set_alert('success','<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Claim submitted successfully.</span>','AdminExpenseApproval.php');
    
    if (mysqli_query($dbconn, $sqlInsert)) {
        $_SESSION['success'] = "Expense claim submitted successfully.";
        header("Location: AdminExpenseApproval.php");
        exit();
    } else {
        $_SESSION['error'] = "Error submitting claim: " . mysqli_error($dbconn);
        header("Location: AdminClaims.php");
        exit();
    }
}
?>

<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Claims</title>
</head>

<body>
    <div class="layout">
        <?php
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Submit Claims', $adminName); ?>

            <div class="container">
                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <h2>Admin Submit new Claim</h2>
                        <a class="btn btn-danger" href="AdminExpenseApproval.php">Back</a>
                    </div>
                    <form method="post" action="AdminClaims.php">
                    <label for="Description">Description:</label>
                    <input type="text" id="Description" name="Description">
                    <label for="Amount">Amount:</label>
                    <input type="number" id="Amount" name="Amount" step="0.01">
                    <label for="Category">Category:</label>
                    <select id="Category" name="Category">
                        <option value="">Select a category</option>
                        <?php
                        $sqlCategory = "SELECT * FROM expensecategory ORDER BY CategoryName ASC";
                        $categories = mysqli_query($dbconn, $sqlCategory) or die("Error: " . mysqli_error($dbconn));
                        while ($row = mysqli_fetch_assoc($categories)) {
                            echo "<option value='" . $row['CategoryID'] . "'>" . $row['CategoryName'] . "</option>";
                        }
                        ?>
                    </select>
                    <label for="ClaimDate">Claim Date:</label>
                    <input type="date" id="ClaimDate" name="ClaimDate">
                    <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Submit Claim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</html>