<?php
session_start();
include("dbconn.php");
include("function.php");

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

            $claimamount = "SELECT c.Amount
                              FROM ExpenseClaim c 
                              WHERE c.ClaimID = $claimID";

            $result = mysqli_query($dbconn, $claimamount) or die("Error fetching amounts: " . mysqli_error($dbconn));
            $rowAmounts = mysqli_fetch_assoc($result);

            if ($rowAmounts) {
                $claimamount = $rowAmounts['Amount'];
            }

            $remainBudgetResult = mysqli_query($dbconn, "SELECT RemainAmount 
                                                         FROM Budget b 
                                                         JOIN Employee e ON b.DepartmentID = e.DepartmentID
                                                         JOIN ExpenseClaim c ON e.EmployeeID = c.EmployeeID 
                                                         WHERE c.ClaimID = $claimID") or die("Error fetching remaining budget: " . mysqli_error($dbconn));
            $remainBudgetRow = mysqli_fetch_assoc($remainBudgetResult);

            //update budget and approve is request balance is sufficient
            if ($claimamount > $remainBudgetRow['RemainAmount']) {
                $sqlstatus = "UPDATE ExpenseClaim SET Status='Rejected' WHERE ClaimID = $claimID";
                mysqli_query($dbconn, $sqlstatus) or die("Error updating claim status: " . mysqli_error($dbconn));
                set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Auto-rejected this claim. Insufficient remaining department budget.</span>', 'AdminExpenseApproval.php');
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
                $sqlstatus = "UPDATE ExpenseClaim 
                              SET Status='Approved' WHERE ClaimID = $claimID";
                mysqli_query($dbconn, $sqlstatus) or die("Error updating claim status: " . mysqli_error($dbconn));

                //approval message
                set_alert('success', '<span class="menu-item-wrapper"><img src="IconSuccess.svg" alt="Checkmark" width="20" height="20" style="margin-right: 5px;"> Claim approved successfully.</span>', 'AdminExpenseApproval.php');
            }
        } else if (isset($_POST['reject'])) {
            // 4. Updated status query (Removed quotes around $claimID)
            $sqlstatus = "UPDATE ExpenseClaim SET Status='Rejected' WHERE ClaimID = $claimID";
            mysqli_query($dbconn, $sqlstatus) or die("Error updating claim status: " . mysqli_error($dbconn));
            set_alert('error', '<span class="menu-item-wrapper"><img src="IconError.svg" alt="Error" width="20" height="20" style="margin-right: 5px;"> Claim rejected successfully.</span>', 'AdminExpenseApproval.php');
        }
    }
}
