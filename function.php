<?php
include("dbconn.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

function set_alert($type,$message,$location) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
    header("Location: " . $location);
    exit();
}
function show_alert(){
    if(!empty($_SESSION['alert'])){ 
        $alert = $_SESSION['alert'];
        if(isset($_SESSION['alert'])){
            echo '<div class="alert alert-' . $alert['type'] . '" id="alert">' . '<span>' . $alert['message'] . '</span>' .
                 '<button type="button" class="close-btn" onclick="document.getElementById(\'alert\').style.display=\'none\';">x</button>' .
                 '</div>';
            
            unset($_SESSION['alert']);
        }
    }
}
?>