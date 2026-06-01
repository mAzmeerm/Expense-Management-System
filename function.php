<?php
include("dbconn.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function money($amount) {
    return 'RM ' . number_format((float)$amount, 2);
}



function require_login() {
    if (!isset($_SESSION['UserID']) || $_SESSION['UserID'] === '') {
        header("Location: login.php");
        exit();
    }
}

function set_alert($type, $message, $location) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
    header("Location: " . $location);
    exit();
}

function show_alert() {
    if (!empty($_SESSION['alert'])) { 
        $alert = $_SESSION['alert'];

        echo '<div class="alert alert-' . $alert['type'] . '" id="alert">' .
             '<span>' . $alert['message'] . '</span>' .
             '<button type="button" class="close-btn" onclick="document.getElementById(\'alert\').style.display=\'none\';">x</button>' .
             '</div>';

        unset($_SESSION['alert']);
    }
}

function show_header($title,$Name) {
    echo '<header>';
    echo '<strong>' . $title . '</strong>';
    if (isset($_SESSION['UserID'])) {
        echo '<span>Welcome, ' . $Name. '</span>';
    }
    echo '</header>';
}
?>