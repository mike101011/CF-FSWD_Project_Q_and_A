<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
if ((!isset($_GET["q_id"])) && (!isset($_GET["a_id"]))) {
    header("Location: ../index.php");
    exit;
}
require_once "../components/db_connect.php";
if (isset($_SESSION["adm"])) {
    $u_id = $_SESSION["adm"];
    $address = "dashboard.php";
} else {
    $u_id = $_SESSION["user"];
    $address = "user-home.php";
}
$class = "d-none";

if (isset($_GET["q_id"])) {
    $q_id = $_GET["q_id"];
    $class = $userclass = "";
    $sql = "SELECT * FROM (tags JOIN quetag ON tags.t_id=quetag.fk_t_id JOIN questions ON quetag.fk_q_id=questions.q_id) LEFT JOIN users ON questions.fk_u_id=users.u_id WHERE q_id='$q_id';";
    $res = mysqli_query($connect, $sql);
    $data = mysqli_fetch_assoc($res);
    $title = $data["q_title"];
    $q_txt = $data["q_txt"];
    $q_date = $data["q_date"];
    $q_vote = $data["q_vote"];
    $tag = $data["title"];
    if ($data["fk_u_id"]) {
        $val = $data["l_name"] . "-" . $data["fk_u_id"];
        if ($data["fk_u_id"] == $u_id) {
            $userclass = "d-none";
        }
    } else {
        $val = "Fromer user";
    }
    if ($data["q_resolved"] == 1) {
        $val2 = "Answered";
        $resclass = "resolved";
    } else {
        $val2 = "Open";
        $resclass = "";
    }
    if (isset($_POST["submit"])) {
        $sql = "DELETE * FROM questions WHERE q_id='$q_id';";
        if (mysqli_query($connect, $sql)) {
            $class = "d-none";
            echo "Success!";
            header("Location: refresh:1;url=../users/" . $address);
        } else {
            echo "Error!";
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
    <title>Delete</title>
    <?php require_once "../components/boot-css.php" ?>
</head>

<body>
    <div class="container <?php echo $class ?>">
        <h2 class="text-center">Do you want to delete this question?</h2>
        <div class="question-top">
            <h5 class="text-center"><?php echo $title; ?></h5>
            <span class="<?php echo $resclass; ?>"><?php echo $val2; ?></span>
        </div>
        <div>
            <h5 class="text-right q-vote">Votes: <?php echo $q_vote; ?></h5>
            <?php echo $q_txt; ?>
        </div>
        <h6>Tag: <?php echo $tag; ?></h6>
        <hr>
        <form method="post" enctype="multipart/form-data">
            <button name="submit" class="btn btn-danger">Proceed</button>
            <a href="" class="btn btn-warning">No, go back</a>
        </form>

    </div>
</body>

</html>