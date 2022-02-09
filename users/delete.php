<?php
session_start();
if ((!isset($_SESSION["user"])) && (!isset($_SESSION["adm"]))) {
    header("Location: ../index.php");
    exit;
}
if (isset($_SESSION["user"])) {
    header("Location: user-home.php");
    exit;
}
require_once "../components/db_connect.php";
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "SELECT * FROM users WHERE u_id='$id';";
    $res = mysqli_query($connect, $sql);
    $data = mysqli_fetch_assoc($res);
    $fname = $data["f_name"];
    $lname = $data["l_name"];
    $b_date = $data["b_date"];
    $pic = $data["picture"];
    $role = $data["role"];
    $status = $data["status"];
}
$class = "d-none";
$class2 = "";
$message = "";
if (isset($_POST["submit"])) {
    $class2 = "d-none";
    $id = $_POST["id"];
    $pic = $_POST["pic"];
    ($pic == "default-user.png") ?: unlink("../pictures/$pic");

    $sql = "DELETE FROM users WHERE u_id = {$id}";
    if ($connect->query($sql) === TRUE) {
        $class = "alert alert-success";
        $message = "Successfully Deleted!";
        header("refresh:3;url=dashboard.php?Users");
    } else {
        $class = "alert alert-danger";
        $message = "The entry was not deleted due to: $connect->error";
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
    <title>Delete User</title>
    <?php require_once '../components/boot-css.php' ?>
</head>

<body>
    <h2 class="text-center">Do you want to delete this user?</h2>
    <div class="container">
        <div class="<?php echo $class; ?>">
            <h4><?php echo $message; ?></h4>
        </div>
        <div class="<?php echo $class2; ?>">
            <div class="left">
                <img src="../pictures/<?php echo $pic; ?>" class="img-fluid" style="max-width: 300px;" alt="Error">
            </div>
            <div class="right">
                <p><strong>First Name:</strong> <?php echo $fname; ?></p>
                <p><strong>Last Name:</strong> <?php echo $lname; ?></p>
                <p><strong>Birth Date:</strong> <?php echo $b_date; ?></p>
                <p><strong>Role:</strong> <?php echo $role; ?></p>
                <p><strong>Status:</strong> <?php echo $status; ?></p>
            </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="pic" value="<?php echo $pic; ?>">
                <div class="buttons">
                    <a href="dashboard.php?Users" class="btn btn-primary">No, go back!</a>
                    <button class="btn btn-danger" name="submit">Yes, proceed!</button>
                </div>
            </form>
        </div>


    </div>

</body>

</html>