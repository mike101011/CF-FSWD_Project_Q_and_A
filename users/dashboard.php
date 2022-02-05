<?php
session_start();
if ((!isset($_SESSION["user"])) && (!isset($_SESSION["adm"]))) {
    header("Location: ../index.php");
}
if (isset($_SESSION["user"])) {
    header("Location: user-home.php");
}
require_once "../components/db_connect.php";
if (isset($_SESSION["adm"])) {
    $id = $_SESSION["adm"];
    $sql = "SELECT * FROM users WHERE u_id='$id';";
    $res = mysqli_query($connect, $sql);
    $data = mysqli_fetch_assoc($res);
    $fname = $data["f_name"];
    $lname = $data["l_name"];
    $pic = $data["picture"];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php require_once "../components/boot-css.php" ?>
</head>

<body>
    <a href="logout.php" class="btn btn-secondary">Log out</a>
    <h5>Hello, <?php echo $fname . " " . $lname; ?>!</h5>
    <img src="../pictures/<?php echo $pic; ?>" alt="">
</body>

</html>