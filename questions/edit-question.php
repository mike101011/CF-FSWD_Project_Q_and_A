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
    $sql = "SELECT * FROM questions JOIN quetag ON questions.q_id=quetag.fk_q_id JOIN tags on quetag.fk_t_id=tags.t_id WHERE q_id='$q_id' ORDER BY t_id ASC; ";
    $res = mysqli_query($connect, $sql);
    $data = $res->fetch_all(MYSQLI_ASSOC);
    if ((!isset($_SESSION["adm"])) && ($u_id !== $data[0]["fk_u_id"])) {
        header("Location: view-question.php?id=" . $q_id . "");
        exit;
    }
    $q_title = $data[0]["q_title"];
    $q_txt = $data[0]["q_txt"];
    $title0 = $data[0]["title"];
    if ($data[1]["title"]) {
        $title1 = $data[1]["title"];
    } else {
        $title1 = "";
    }
    if ($data[2]["title"]) {
        $title2 = $data[2]["title"];
    } else {
        $title2 = "";
    }
    $titleError = $txtError = $tagError = "";
    $error = false;
    if (isset($_POST["submit"])) {
        $q_title = $_POST["title"];
        $q_txt = $_POST["q_txt"];
        $title1 = $_POST["tag1"];
        $title2 = $_POST["tag2"];
        $title3 = $_POST["tag3"];
        switch (true) {
            case (empty($q_title)):

                $titleError = "Question must have a title.";
                break;
            case (empty($q_txt)):

                $txtError = "Please formulate your question.";
                break;
            case ((empty($title1)) && (empty($title2)) && (empty($title3))):
                $tagError = "Please choose a tag.";
                break;


            default:
                $tags = array();
                for ($i = 1; $i < 4; $i++) {
                    $tag = $_POST["tag" . $i];
                    if (!in_array($tag, $tags)) {
                        array_push($tags, $tag);
                    }
                }

                $sql2 = "UPDATE questions SET q_title='$q_title', q_txt='$q_txt' WHERE q_id='$q_id';";
                mysqli_query($connect, $sql2);
                $sql = "DELETE FROM quetag WHERE fk_q_id='$q_id';";
                mysqli_query($connect, $sql2);
                $tag_id = "";
                for ($i = 0; $i < count($tags); $i++) {
                    $tag = $tags[$i];
                    $taqquer = "SELECT * FROM tags WHERE title='$tag';";
                    $res = mysqli_query($connect, $taqquer);
                    if (mysqli_num_rows($res) > 0) {
                        $data_2 = mysqli_fetch_assoc($res);
                        $tag_id = $data_2["t_id"];
                    } else {
                        $quer2 = "INSERT INTO tags(title) VALUES('$tag');";
                        mysqli_query($connect, $quer2);
                        $quer3 = "SELECT t_id FROM tags WHERE title='$tag';";
                        $restg = mysqli_query($connect, $quer3);
                        $datatg = mysqli_fetch_assoc($restg);
                        $tag_id = $datatg["t_id"];
                    }
                    $sqlfinal = "INSERT INTO quetag(fk_q_id,fk_t_id) VALUES('$q_id','$tag_id');";
                    mysqli_query($connect, $sqlfinal);
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
                            <th></th>
                            <td>
                                <h5>Choose at least one tag</h5>
                            </td>
                        </tr>

                        <tr>
                            <th>Tag 1</th>
                            <td>
                                <input type="text" class="form-control" name="tag1" value="<?php echo $title0; ?>">
                                <span class="text-danger"><?php echo $tagError; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tag 2</th>
                            <td>
                                <input type="text" class="form-control" name="tag2" value="<?php echo $title1; ?>">

                            </td>
                        </tr>
                        <tr>
                            <th>Tag 3</th>
                            <td>
                                <input type=" text" class="form-control" name="tag3" value="<?php echo $title2; ?>">

                            </td>
                        </tr>
                    </table>
                </fieldset>
                <button type="submit" class="btn btn-secondary" name="submit"> Post!</button>
            </form>
            <a href="delete.php?q_id=<?php echo $q_id; ?>" class="btn btn-danger">Delete</a>
        </div>

    </div>
</body>

</html>