<?php
session_start();
unset($_SESSION['admin_auth_password']);
unset($_SESSION['error']);
session_write_close();
header("Location: ../main.php");
exit();
?>