<?php
session_start();
if (!isset($_SESSION["adm"])) {
    header("Location: ../index.php");
    exit;
} else {
    require_once "../components/db_connect.php";
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
            $sql = "INSERT INTO tags(title,t_description) VALUES('$title','$description')";
            if (mysqli_query($connect, $sql)) {
                echo "Success";
                header("refresh:1;url=../users/dashboard.php");
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
    <title>Create Tag</title>
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
        <h3 class="text-center">New Tag</h3>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off" enctype="multipart/form-data">
            <fieldset>
                <table class="table">
                    <tr>
                        <th>Tag Name</th>
                        <td><input type="text" class="form-control" name="tag" placeholder="Tag Name"></td>
                        <span class="text-danger"><?php echo $tagError; ?></span>
                    </tr>
                    <tr>
                        <th>Description (optional)</th>
                        <td><textarea name="description" class="form-control" cols="15" rows="10" placeholder="Short description"></textarea></td>

                    </tr>
                </table>
                <button type="submit" name="submit" class="btn btn-secondary">Submit</button>
            </fieldset>
        </form>
    </div>
</body>

</html>