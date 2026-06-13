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
?>

<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Claims</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminClaims.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Claims', $adminName); ?>

            <div class="container">
                <div class="card">
                    <h2>Admin Submit new Claim</h2>
                    <p>This is where you can add new claims.</p>
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
                </div>
            </div>
        </div>
    </div>

</html>