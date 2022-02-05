<?php
session_start();
if (isset($_SESSION['user']) != "") {
    header("Location: users/userhome.php"); // redirects to home.php
}
if (isset($_SESSION['adm']) != "") {
    header("Location: users/dashboard.php"); // redirects to home.php
}
require_once "components/db_connect.php";
require_once "components/file_upload.php";
$email = $pass = $emailError = "";
$errclass = "d-none";
$error = false;
if (isset($_POST["submit-btn"])) {
    $email = inpTransf($_POST["email"]);
    $pass = inpTransf($_POST["pass"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $emailError = "Please enter valid email address.";
    } else {
        $pass = hash('sha256', $pass);
        $sql = "SELECT * FROM users WHERE email='$email' AND pass='$pass'";
        $res = mysqli_query($connect, $sql);
        if (mysqli_num_rows($res) == 0) {
            $error = true;
            $errclass = "";
        }
        if (!$error) {
            $row = mysqli_fetch_assoc($res);
            if ($row["role"] == "adm") {
                $_SESSION["adm"] = $row["u_id"];
                header("Location: users/dashboard.php");
            } else {
                $_SESSION["user"] == $row["u_id"];
                header("Location: users/userhome.php");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programming Q and A</title>
    <?php require_once "components/boot-css.php" ?>
</head>

<body>
    <h1 class="text-center">Welcome to Programming Q and A!</h1>

    <div class="container">
        <div class="<?php echo $errclass; ?> text-danger">
            <h6>Wrong credentials! Try again.</h6>
        </div>
        <form class="w-75" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off" enctype="multipart/form-data">
            <div> <input type="text" class="form-control" name="email" placeholder="Email">
            </div>
            <span class="text-danger"><?php echo $emailError; ?></span>
            <div><input type="password" class="form-control" name="pass" placeholder="Password"></div>
            <button type="submit" name="submit-btn" class="btn btn-secondary">Sign in!</button>

        </form>
    </div>

</body>

</html>