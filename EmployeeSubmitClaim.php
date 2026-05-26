<?php
session_start();
include("dbconn.php");
include("function.php");
require_login();

$loggedInUser = $_SESSION['UserID'];

// 1. Handle the form submission
if (isset($_POST['submit_claim'])) {

    // Grab exact inputs directly from the form
    $description = $_POST['Description'];
    $amount = $_POST['Amount'];
    $date = $_POST['Date'];
    $categoryID = $_POST['Category'];
    $status = 'Pending';

    // 2. Insert into the database
    $sql = "INSERT INTO expenseclaim (EmployeeID, CategoryID, Description, Amount, ClaimDate, Status) 
            VALUES ('$loggedInUser', '$categoryID', '$description', '$amount', '$date', '$status')";

    $query = mysqli_query($dbconn, $sql);

    // 3. Check if it worked and show alert
    if ($query) {
        set_alert('success', 'Your claim has been submitted successfully.', 'EmployeeSubmitClaim.php');
    } else {
        set_alert('error', 'Failed to submit claim. Please try again.', 'EmployeeSubmitClaim.php');
    }
}

// 4. Fetch categories for the dropdown menu
$sqlCategories = "SELECT * FROM expensecategory ORDER BY CategoryName ASC";
$categories = mysqli_query($dbconn, $sqlCategories) or die("Error: " . mysqli_error($dbconn));
?>
<!DOCTYPE html>
<html>

<head>
    <title>Submit New Expense Claim</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'EmployeeSubmitClaim.php';
        include 'EmployeeSidebar.php';
        ?>

        <div class="main-content">
            <header>
                <strong>Submit New Expense Claims</strong>
                <span>Welcome, Employee</span>
            </header>

            <div class="container">
                <?php show_alert(); ?>

                <div class="card">
                    <form method="post" action="EmployeeSubmitClaim.php">

                        <label for="Description">Description:</label>
                        <input type="text" id="Description" name="Description" required
                            placeholder="E.g., Client Meeting Lunch">

                        <label for="Amount">Amount:</label>
                        <input type="number" id="Amount" name="Amount" step="0.01" required placeholder="0.00">

                        <label for="Date">Date:</label>
                        <input type="date" id="Date" name="Date" required>

                        <label for="Category">Category:</label>
                        <select id="Category" name="Category" required>
                            <option value="" disabled selected>Select Category</option>
                            <?php
                            while ($categoryRow = mysqli_fetch_assoc($categories)) {
                                ?>
                                <option value="<?= $categoryRow['CategoryID'] ?>">
                                    <?= htmlspecialchars($categoryRow['CategoryName']) ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>

                        <br><br>

                        <button type="submit" name="submit_claim" class="btn btn-primary" style="width: 100%;">Submit
                            Claims</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>