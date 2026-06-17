<?php
include("dbconn.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function money($amount)
{
    return 'RM ' . number_format((float) $amount, 2);
}



function require_login()
{
    if (!isset($_SESSION['UserID']) || $_SESSION['UserID'] === '') {
        header("Location: login.php");
        exit();
    }
}

function set_alert($type, $message, $location)
{
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
    header("Location: " . $location);
    exit();
}

function show_alert()
{
    if (!empty($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];

        echo '<div class="alert alert-' . $alert['type'] . '" id="alert">' .
            '<span>' . $alert['message'] . '</span>' .
            '<button type="button" class="close-btn" onclick="document.getElementById(\'alert\').style.display=\'none\';">x</button>' .
            '</div>';

        unset($_SESSION['alert']);
    }
}

function show_header($title, $Name)
{
    echo '<header>';
    echo '<strong>' . $title . '</strong>';
    if (isset($_SESSION['UserID'])) {
        echo '<span>Welcome, ' . $Name . '</span>';
    }
    echo '</header>';
}


function show_pagination($page, $totalPages, $search = '', $filterKey = '', $filterValue = '')
{
    if ($totalPages <= 1)
        return; // Don't show anything if there's only 1 page

    $s = urlencode($search);

    $urlParams = "&search=$s";
    if ($filterKey !== '' && $filterValue !== '') {
        $urlParams .= "&" . urlencode($filterKey) . "=" . urlencode($filterValue);
    }

    echo '<div class="pagination">';

    // 1. Previous Button
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . $urlParams . "' class='btn-page'>&laquo; Prev</a>";
    }

    // 2. Number Buttons
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($page == $i) ? 'active' : '';
        echo "<a href='?page=$i" . $urlParams . "' class='btn-page $active'>$i</a>";
    }

    // 3. Next Button
    if ($page < $totalPages) {
        echo "<a href='?page=" . ($page + 1) . $urlParams . "' class='btn-page'>Next &raquo;</a>";
    }

    echo '</div>';
}
?>