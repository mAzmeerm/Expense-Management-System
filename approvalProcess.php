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
?>
