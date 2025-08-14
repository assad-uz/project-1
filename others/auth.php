// includes/auth.php
session_start();
function checkAdmin() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: ../auth/login.php");
        exit;
    }
}

function checkUser() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'user') {
        header("Location: ../auth/login.php");
        exit;
    }
}