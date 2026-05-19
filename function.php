<?php
include("dbconn.php");

function money($amount) {
    return 'RM ' . number_format((float)$amount, 2);
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if (!isset($_SESSION['UserID']) || $_SESSION['UserID'] === '') {
    header("Location: login.php");
    exit();
}

?>