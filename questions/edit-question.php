<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
if (!isset($_GET["q_id"])) {
    header("Location: ../index.php");
    exit;
} else {
    require_once "../components/db_connect.php";
    require_once "../components/functions.php";
    $q_id = $_GET["q_id"];
    $u_id = sessFct();
    $sql = "SELECT * FROM questions JOIN quetag ON questions.q_id=quetag.fk_q_id JOIN tags on quetag.fk_t_id=tags.t_id WHERE q_id='$q_id'; ";
    $res = mysqli_query($connect, $sql);
    $data = mysqli_fetch_assoc($res);
    if ((!isset($_SESSION["adm"])) && ($u_id !== $data["fk_u_id"])) {
        header("Location: view-question.php?id=" . $q_id . "");
        exit;
    }
    $q_title = $data["q_title"];
    $q_txt = $data["q_txt"];
    $title = $data["title"];
    $titleError = $txtError = $tagError = "";
    $error = false;
    if (isset($_POST["submit"])) {
        $q_title = $_POST["title"];
        $q_txt = $_POST["q_txt"];
        $title = $_POST["tag"];
        switch (true) {
            case (empty($q_title)):

                $titleError = "Question must have a title.";
                break;
            case (empty($q_txt)):

                $txtError = "Please formulate your question.";
                break;
            case (empty($title)):
                $tagError = "Please choose a tag.";
                break;


            default:


                $sql2 = "UPDATE questions SET q_title='$q_title', q_txt='$q_txt' WHERE q_id='$q_id';";
                mysqli_query($connect, $sql2);
                if ($title !== $data["title"]) {
                    $sql3 = "SELECT * FROM tags WHERE title='$title';";
                    $res3 = mysqli_query($connect, $sql3);
                    if (mysqli_num_rows($res3) > 0) {
                        $data3 = mysqli_fetch_assoc($res3);
                        $t_id = $data3["t_id"];
                        $sql4 = "UPDATE quetag SET fk_t_id='$t_id' WHERE fk_q_id='$q_id';";
                        mysqli_query($connect, $sql4);
                    } else {
                        $sql5 = "INSERT INTO tags(title) VALUES('$title')";
                        mysqli_query($connect, $sql5);
                        $sql5 = "SELECT * FROM tags WHERE title='$title';";
                        $res5 = mysqli_query($connect, $sql5);
                        $data5 = mysqli_fetch_assoc($res5);
                        $t_id = $data5["t_id"];
                        $sql5 = "DELETE FROM quetag WHERE quetag.fk_q_id='$q_id';";
                        mysqli_query($connect, $sql5);
                        $sql5 = "INSERT INTO quetag(fk_q_id,fk_t_id) VALUES('$q_id','$t_id');";
                        mysqli_query($connect, $sql5);
                    }
                }
                break;
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
    <title>Document</title>
    <?php require_once "../components/boot-css.php" ?>
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
        <h2 class="text-center">Post Your Question Below</h2>
        <div>
            <form method="post" enctype="multipart/form-data">
                <fieldset>
                    <table class="table">
                        <tr>
                            <th>Title</th>
                            <td>
                                <input type="text" class="form-control" name="title" value="<?php echo $q_title; ?>">
                                <span class="text-danger"><?php echo $titleError; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Your question</th>
                            <td>
                                <textarea class="form-control" name="q_txt" cols="40" rows="20"><?php echo $q_txt; ?></textarea>
                                <span class="text-danger"><?php echo $txtError; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Choose one tag</th>
                            <td>
                                <input type="text" class="form-control" name="tag" value="<?php echo $title; ?>">
                                <span class="text-danger"><?php echo $tagError; ?></span>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <button type="submit" class="btn btn-secondary" name="submit"> Post!</button>
            </form>
        </div>

    </div>
</body>

</html>