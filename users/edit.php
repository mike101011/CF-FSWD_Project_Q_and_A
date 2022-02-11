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
        require_once "../components/file_upload.php";
        $id = $_GET["id"];
        $sql = "SELECT * FROM users WHERE u_id='$id';";
        $res = mysqli_query($connect, $sql);
        $data = mysqli_fetch_assoc($res);
        $fname = $data["f_name"];
        $lname = $data["l_name"];
        $b_date = $data["b_date"];
        $email = $data["email"];
        $status = $data["status"];
        $pass1 = $pass2 = $pass3 = $passError = $fnameError = $emailError = $message = $opt = $opttxt = $stattxt = "";
        if ($status == "ok") {
            $stattxt = "OK";
            $opt = "banned";
            $opttxt = "Banned";
        } else {
            $stattxt = "Banned";
            $opt = "ok";
            $opttxt = "OK";
        }
        if ($data["web_page"]) {
            $webpage = $data["web_page"];
        } else {
            $webpage = null;
        }
        $pic = $data["picture"];
        $class = 'd-none';
        $updateclass = "d-none";
        if (isset($_SESSION["user"])) {
            $previous = "user-home.php";
        }
        if (isset($_SESSION["adm"])) {
            $previous = "dashboard.php";
            $updateclass = "";
        }

        if (isset($_POST["submit"])) {
            $error = false;
            if ((isset($_SESSION["user"])) && ($_SESSION["user"] != $_POST["id"])) {
                header("Location: user-home.php");
                exit;
            } else {
                $id = $_POST["id"];
                $fname = $_POST["fname"];
                $lname = $_POST["lname"];
                $b_date = $_POST["b_date"];
                $email = $_POST["email"];
                $pass1 = $_POST["pass1"];
                $pass2 = $_POST["pass2"];
                $pass3 = $_POST["pass3"];
                $webpage = $_POST["webpage"];
                $status = $_POST["status"];
                $pic = $_POST["image"];
                $pictureArray = file_upload($_FILES['picture']); //file_upload() called

                $uploadError = '';
                if (empty($fname) || empty($lname)) {
                    $error = true;
                    $fnameError = "Please enter your full name and surname";
                } else if (strlen($fname) < 3 || strlen($lname) < 3) {
                    $error = true;
                    $fnameError = "Name and surname must have at least 3 characters.";
                } else if (!preg_match("/^[a-zA-Z]+$/", $fname) || !preg_match("/^[a-zA-Z]+$/", $lname)) {
                    $error = true;
                    $fnameError = "Name and surname must contain only letters and no spaces.";
                }

                //basic email validation
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = true;
                    $emailError = "Please enter valid email address.";
                }
                if ((!empty($pass1)) && ($pass2 !== $pass3)) {
                    $error = true;
                    $passError = "New pasword must be confirmed!";
                } elseif ((!empty($pass1)) && (strlen($pass2) < 6)) {
                    $error = true;
                    $passError = "New password must have at least 6 characters.";
                } else {
                    $str1 = $str2 = "";
                    if (isset($_SESSION["adm"])) {
                        $str1 = "status='$status', ";
                        $str2 = ", status='$status' ";
                    }
                    if (!$error) {
                        $sql = "SELECT * FROM users WHERE u_id='$id';";
                        $res = mysqli_query($connect, $sql);
                        $data = mysqli_fetch_assoc($res);
                        $password = $data["pass"];
                        $passError = "Password unchanged.";
                        if (!empty($pass1)) {
                            $pass1 = inpTransf($pass1);
                            $pass2 = inpTransf($pass2);
                            $pass3 = inpTransf($pass3);
                            if ($password == hash('sha256', $pass1)) {
                                $password = hash('sha256', $pass2);
                                $passError = "Password modified!";
                            }
                        }

                        if ($pictureArray->error === 0) {
                            ($_POST["image"] == "default-user.png") ?: unlink("../pictures/{$_POST["image"]}");
                            $pic = $pictureArray->fileName;
                            $sql = "UPDATE users SET f_name = '$fname', l_name = '$lname', b_date = '$b_date',email = '$email', pass='$password', web_page='$webpage'," . $str1 . "  picture = '$pic' WHERE u_id = {$id}";
                        } else {
                            $sql = "UPDATE users SET f_name = '$fname', l_name = '$lname', b_date = '$b_date',email = '$email', pass='$password', web_page='$webpage'" . $str2 . " WHERE u_id = {$id}";
                        }
                        if (mysqli_query($connect, $sql) === true) {
                            $class = "alert alert-success";
                            $message = "The record was successfully updated";
                            $uploadError = ($pictureArray->error != 0) ? $pictureArray->ErrorMessage : '';
                            header("refresh:2;url=edit.php?id={$id}");
                        } else {
                            $class = "alert alert-danger";
                            $message = "Error while updating record : <br>" . $connect->error;
                            $uploadError = ($pictureArray->error != 0) ? $pictureArray->ErrorMessage : '';
                            header("refresh:2;url=edit.php?id={$id}");
                        }
                    }
                }
            }
        }
    }
}
mysqli_close($connect);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <?php require_once '../components/boot-css.php' ?>
    <style type="text/css">
        .img-thumbnail {
            width: 70px !important;
            height: 70px !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="<?php echo $class; ?>" role="alert">
            <p><?php echo ($message) ?? ''; ?></p>
            <p><?php echo ($uploadError) ?? ''; ?></p>
            <p><?php echo $passError; ?></p>
        </div>

        <h2>Edit Profile</h2>
        <img class='img-thumbnail rounded-circle' src='../pictures/<?php echo $pic ?>' alt="<?php echo $fname ?>">
        <form method="post" enctype="multipart/form-data">
            <table class="table">
                <tr>
                    <th>First Name</th>
                    <td><input class="form-control" type="text" name="fname" placeholder="First Name" value="<?php echo $fname ?>" /></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><input class="form-control" type="text" name="lname" placeholder="Last Name" value="<?php echo $lname ?>" /></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input class="form-control" type="email" name="email" placeholder="Email" value="<?php echo $email ?>" /></td>
                </tr>
                <tr>
                    <th>Date of birth</th>
                    <td><input class="form-control" type="date" name="b_date" placeholder="Date of birth" value="<?php echo $b_date ?>" /></td>
                </tr>
                <tr>
                    <th>Web Page</th>
                    <td><input type="text" class="form-control" name="webpage" placeholder="Url here!" value="<?php echo $webpage; ?>"></td>
                </tr>
                <tr>
                    <th>Optional: Change Password</th>
                    <td><input type="password" class="form-control" name="pass1" placeholder="Current password"></td>
                </tr>
                <tr>
                    <th>New Password</th>
                    <td><input type="password" class="form-control" name="pass2" placeholder="New password"></td>
                </tr>


                <tr>
                    <th>Confirm New Password</th>
                    <td><input type="password" class="form-control" name="pass3" placeholder="Confirm new password"></td>
                </tr>
                <tr class="<?php echo $updateclass; ?>">
                    <th>Status</th>
                    <td><select name="status" class="form-control">
                            <option value="<?php echo $status; ?>" selected><?php echo $stattxt; ?></option>
                            <option value="<?php echo $opt; ?>"><?php echo $opttxt; ?></option>

                        </select>


                    </td>
                </tr>
                <tr>
                    <th>Picture</th>
                    <td><input class="form-control" type="file" name="picture" /></td>
                </tr>
                <tr>
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input type="hidden" name="image" value="<?php echo $pic ?>" />
                    <td><button name="submit" class="btn btn-success" type="submit">Save Changes</button></td>
                    <td><a href='<?= $previous ?>'> <button class="btn btn-warning" type="button">Back</button></a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>

</html>