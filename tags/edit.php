<?php
session_start();
if ((!isset($_SESSION["adm"])) || (!isset($_GET["t_id"]))) {
    header("Location: ../index.php");
    exit;
} else {
    require_once "../components/db_connect.php";
    $t_id = $_GET["t_id"];
    $message = "";
    $sql = "SELECT * FROM tags WHERE t_id='$t_id';";
    $res = mysqli_query($connect, $sql);
    if ($res->num_rows == 0) {
        $message = "No such tag exists.";
    } else {
        $data = mysqli_fetch_assoc($res);
        $title = $data["title"];
        if ($data["t_description"]) {
            $description = $data["t_description"];
        } else {
            $description = "";
        }
    }
    $tagError = "";
    $error = false;
    if (isset($_POST["submit"])) {
        $title = $_POST["tag"];
        if (!empty($_POST["description"])) {
            $description = $_POST["description"];
        } else {
            $description = null;
        }
        if (empty($title)) {
            $error = true;
            $tagError = "Tag must have a title.";
        }
        if (!$error) {
            $sql = "UPDATE tags SET title='$title', t_description='$description' WHERE t_id='$t_id';";
            if (mysqli_query($connect, $sql)) {
                echo "Success";
                header("refresh:1;url=../users/dashboard.php");
            } else {
                echo $title;
                echo $description;
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tag</title>
    <?php require_once "../components/boot-css.php"; ?>
    <style>
        fieldset {
            margin: auto;
            margin-top: 100px;
            width: 60%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3 class="text-center">Edit Tag</h3>
        <form method="post" enctype="multipart/form-data">
            <fieldset>
                <table class="table">
                    <tr>
                        <th>Tag Name</th>
                        <td><input type="text" class="form-control" name="tag" value="<?php echo $title; ?>"></td>
                        <span class="text-danger"><?php echo $tagError; ?></span>
                    </tr>
                    <tr>
                        <th>Description (optional)</th>
                        <td><textarea name="description" class="form-control" cols="15" rows="10" placeholder="Short description"><?php echo $description; ?></textarea></td>
                    </tr>
                </table>
                <button type="submit" name="submit" class="btn btn-secondary">Submit</button>
            </fieldset>
        </form>
    </div>
</body>

</html>