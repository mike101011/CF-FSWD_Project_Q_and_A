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