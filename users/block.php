<?php
session_start();
if (!isset($_SESSION["banned"])) {
    if (isset($_SESSION["user"])) {
        header("Location: user-home.php");
        exit;
    } else if (isset($_SESSION["adm"])) {
        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: ../index.php");
    }
} else {
    unset($_SESSION["banned"]);
    session_unset();
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block Page</title>
    <?php require_once "../components/boot-css.php"; ?>
</head>

<body>
    <div class="container">
        <h1 class="text-center">You have been blocked!</h1>
        <p>Please contact the administrator for further information.</p>
        <a href="..index.php" class="btn btn-secondary">Go back</a>
    </div>

</body>

</html>