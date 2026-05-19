<?php
session_start();
include("dbconn.php");

// Get the ClaimID and force it to be an integer for absolute safety
$claimID = (int)$_REQUEST["ClaimID"];

// 1. No quotes around $claimID because it is an integer
$sql = "SELECT * FROM ExpenseClaim WHERE ClaimID = $claimID";
$query = mysqli_query($dbconn, $sql) or die("Error fetching claim: " . mysqli_error($dbconn));
$row = mysqli_fetch_assoc($query);


if (!$row) {
    echo "No record found";
} else {
    if ($row['Status'] == "Pending" || $row['Status'] == "pending") {

        if (isset($_POST['approve'])) {

            $claimamount = "SELECT c.Amount, b.SpentAmount 
                              FROM ExpenseClaim c 
                              JOIN Employee e ON c.EmployeeID = e.EmployeeID 
                              JOIN Budget b ON e.DepartmentID = b.DepartmentID 
                              WHERE c.ClaimID = $claimID";

            $result = mysqli_query($dbconn, $claimamount) or die("Error fetching amounts: " . mysqli_error($dbconn));
            $rowAmounts = mysqli_fetch_assoc($result);

            if ($rowAmounts) {
                // 4. Extract the values using their exact database column names
                $claimAmount = $rowAmounts['Amount'];
                $spentAmount = $rowAmounts['SpentAmount'];
            }

            // Fetch the remaining budget for the department
            $remainBudgetResult = mysqli_query($dbconn, "SELECT RemainAmount 
                                                         FROM Budget b 
                                                         JOIN Employee e ON b.DepartmentID = e.DepartmentID
                                                         JOIN ExpenseClaim c ON e.EmployeeID = c.EmployeeID 
                                                         WHERE c.ClaimID = $claimID") or die("Error fetching remaining budget: " . mysqli_error($dbconn));
            $remainBudgetRow = mysqli_fetch_assoc($remainBudgetResult);

            // Calculate the new spent amount if this claim is approved
            $newSpentAmount = $spentAmount + $claimAmount;
            //update budget and approve is request balance is sufficient
            if ($newSpentAmount > $remainBudgetRow['RemainAmount']) {
                $sqlstatus = "UPDATE ExpenseClaim SET Status='Rejected' WHERE ClaimID = $claimID";
                mysqli_query($dbconn, $sqlstatus) or die("Error updating claim status: " . mysqli_error($dbconn));
                $_SESSION['approval_message'] = '<div class="alert alert-danger">auto-reject this claim. Insufficient remaining department budget.</div>';
                header("Location: AdminExpenseApproval.php");
                exit();
            } else {
                // 2. Updated JOIN query (Removed quotes around $claimID at the end)
                $budgetsql = "UPDATE Budget b 
                          JOIN Employee e ON b.DepartmentID = e.DepartmentID 
                          JOIN ExpenseClaim c ON e.EmployeeID = c.EmployeeID
                          SET b.SpentAmount = b.SpentAmount + c.Amount,
                              b.RemainAmount = b.RemainAmount - c.Amount
                          WHERE c.ClaimID = $claimID";

                mysqli_query($dbconn, $budgetsql) or die("Error updating budget: " . mysqli_error($dbconn));

                // 3. Updated status query (Removed quotes around $claimID)
                $sqlstatus = "UPDATE ExpenseClaim SET Status='Approved' WHERE ClaimID = $claimID";
                mysqli_query($dbconn, $sqlstatus) or die("Error updating claim status: " . mysqli_error($dbconn));

                //approval message
                $_SESSION['approval_message'] = '<div class="alert alert-success">Claim approved successfully.</div>';
                header("Location: AdminExpenseApproval.php");
                exit();
            }
        } else if (isset($_POST['reject'])) {
            // 4. Updated status query (Removed quotes around $claimID)
            $sqlstatus = "UPDATE ExpenseClaim SET Status='Rejected' WHERE ClaimID = $claimID";
            mysqli_query($dbconn, $sqlstatus) or die("Error updating claim status: " . mysqli_error($dbconn));
            $_SESSION['approval_message'] = '<div class="alert alert-danger">Claim rejected successfully.</div>';
            header("Location: AdminExpenseApproval.php");
            exit();
        }
    }

    header("Location: AdminExpenseApproval.php");
    exit();
}
