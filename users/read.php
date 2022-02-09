<?php
session_start();
if ((!isset($_SESSION["user"])) && (!isset($_SESSION["adm"]))) {
    header("Location: ../index.php");
    exit;
}
if (!isset($_GET["id"])) {
    header("Location: ../index.php");
    exit;
} else {
    if ((isset($_SESSION["user"])) && ($_SESSION["user"] != $_GET["id"])) {
        header("Location: user-home.php");
        exit;
    } else {
        require_once "../components/db_connect.php";
        $id = $_GET["id"];
        $sql = "SELECT * FROM users WHERE u_id='$id';";
        $res = mysqli_query($connect, $sql);
        $data = mysqli_fetch_assoc($res);
        $fname = $data["f_name"];
        $lname = $data["l_name"];
        $b_date = $data["b_date"];
        $email = $data["email"];
        $points = $data["points"];
        $role = $data["role"];
        $status = $data["status"];
        $pic = $data["picture"];
        if ($data["web_page"]) {
            $webpage = $data["web_page"];
        } else {
            $webpage = null;
        }
        $previous = "";
        $class = "d-none";
        if (isset($_SESSION["user"])) {
            $previous = "user-home.php";
        }
        if (isset($_SESSION["adm"])) {
            $previous = "dashboard.php";
            $class = "";
        }
    }
}
mysqli_close($connect);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Info</title>
    <?php require_once '../components/boot-css.php' ?>
</head>

<body>
    <h2 class="text-center"><?php echo $fname . " " . $lname; ?></h2>
    <div class="container">
        <div class="left">
            <img src="../pictures/<?php echo $pic; ?>" class="img-fluid" style="max-width: 300px;" alt="Error">
        </div>
        <div class="right">
            <p><strong>Birth date:</strong> <?php echo $b_date; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Web Page:</strong> <?php echo $webpage; ?></p>
            <p><strong>Points:</strong> <?php echo $points; ?></p>
            <p class="<?php echo $class; ?>"><strong>Role:</strong> <?php echo $role; ?></p>
            <p class="<?php echo $class; ?>"><strong>Status:</strong> <?php echo $status; ?></p>
        </div>
        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">Edit</a>
    </div>


</body>

</html>