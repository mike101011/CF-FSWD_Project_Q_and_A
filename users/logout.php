<?php
session_start();
if (!isset($_SESSION['adm']) && !isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}
unset($_SESSION['user']);
unset($_SESSION['adm']);
session_unset();
session_destroy();
header("Location: ../index.php");
exit;
