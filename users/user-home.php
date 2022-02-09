<?php
session_start();
if ((!isset($_SESSION["user"])) && (!isset($_SESSION["adm"]))) {
    header("Location: ../index.php");
}
if (isset($_SESSION["adm"])) {
    header("Location: dashboard.php");
}
require_once "../components/db_connect.php";
if (isset($_SESSION["user"])) {
    $id = $_SESSION["user"];
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
    <div class="container">
        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-info">Edit your profile</a>
        <a href="read.php?id=<?php echo $id; ?>" class="btn btn-success">See your data</a>
        <a href="logout.php" class="btn btn-secondary">Log out</a>
        <h5>Hello, <?php echo $fname . " " . $lname; ?>!</h5>
        <img src="../pictures/<?php echo $pic; ?>" alt="" style="max-width: 300px;">
    </div>

</body>

</html>