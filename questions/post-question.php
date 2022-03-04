<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
} else {
    require_once "../components/db_connect.php";
    $titleError = $txtError = $tagError = $id = "";
    if (isset($_POST["submit"])) {
        if (isset($_SESSION["adm"])) {
            $id = $_SESSION["adm"];
        } else {
            $id = $_SESSION["user"];
        }
        $title = $_POST["title"];
        $txt = $_POST["q_txt"];
        $tag1 = $_POST["tag1"];
        $tag2 = $_POST["tag2"];
        $tag3 = $_POST["tag3"];
        switch (true) {
            case (empty($title)):

                $titleError = "Question must have a title.";
                break;
            case (empty($txt)):

                $txtError = "Please formulate your question.";
                break;
            case ((empty($tag1)) && (empty($tag2)) && (empty($tag3))):
                $tagError = "Please choose a tag.";
                break;


            default:

                $date = date("Y.m.d");
                $sqlqu = "INSERT INTO questions(q_title,q_txt,q_date,fk_u_id) VALUES('$title','$txt','$date','$id');";
                mysqli_query($connect, $sqlqu);
                $sql2 = "SELECT q_id FROM questions WHERE q_title='$title' AND q_txt='$txt';";
                $resq = mysqli_query($connect, $sql2);
                $dataq = mysqli_fetch_assoc($resq);
                $q_id = $dataq["q_id"];
                $tags = array();
                for ($i = 1; $i < 4; $i++) {
                    $tag = $_POST["tag" . $i];
                    if (!in_array($tag, $tags)) {
                        array_push($tags, $tag);
                    }
                }
                $tag_id = "";
                for ($i = 0; $i < count($tags); $i++) {
                    $tag = $tags[$i];
                    $taqquer = "SELECT * FROM tags WHERE title='$tag';";
                    $res = mysqli_query($connect, $taqquer);
                    if (mysqli_num_rows($res) > 0) {
                        $data = mysqli_fetch_assoc($res);
                        $tag_id = $data["t_id"];
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
    <title>Post Question</title>
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
                    <table class="table" id="form-table">
                        <tr>
                            <th>Title</th>
                            <td>
                                <input type="text" class="form-control" name="title" placeholder="Title of the question">
                                <span class="text-danger"><?php echo $titleError; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Your question</th>
                            <td>
                                <textarea class="form-control" name="q_txt" cols="40" rows="20" placeholder="Post your question here."></textarea>
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
                                <input type="text" class="form-control" name="tag1">
                                <span class="text-danger"><?php echo $tagError; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tag 2</th>
                            <td>
                                <input type="text" class="form-control" name="tag2">

                            </td>
                        </tr>
                        <tr>
                            <th>Tag 3</th>
                            <td>
                                <input type="text" class="form-control" name="tag3">

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