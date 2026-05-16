<?php
include("dbconn.php");
function money($amount) {
    return 'RM ' . number_format((float)$amount, 2);
}

?>