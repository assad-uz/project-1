<?php session_start();

 unset($_SESSION["s_email"]);
 session_destroy();
 header("location:login.php");
?>