<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireRole($roles) {
    if (!isset($_SESSION['role'])) {
        header("Location: login.php");
        exit();
    }

    if (!in_array($_SESSION['role'], $roles)) {
        header("Location: login.php");
        exit();
    }
}
?>
