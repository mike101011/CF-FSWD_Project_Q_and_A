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
    $data = $res->fetch_all(MYSQLI_ASSOC);
    $title = $data[0]["q_title"];
    $q_txt = $data[0]["q_txt"];
    $q_date = $data[0]["q_date"];
    $q_vote = $data[0]["q_vote"];
    $tag = "";
    for ($i = 0; $i < count(($data)); $i++) {
        $tag .= "<span>" . $data[$i]["title"] . "</span>";
    }
    if (isset($_SESSION["user"])) {
        if ((!$data[0]["fk_u_id"]) || ($u_id !== $data[0]["fk_u_id"])) {
            header("Location: ../users/" . $address);
            exit;
        }
    }
    if ($data[0]["fk_u_id"]) {
        $val = $data[0]["l_name"] . "-" . $data[0]["fk_u_id"];
        if ($data[0]["fk_u_id"] == $u_id) {
            $userclass = "d-none";
        }
    } else {
        $val = "Fromer user";
    }
    if ($data[0]["q_resolved"] == 1) {
        $val2 = "Answered";
        $resclass = "resolved";
    } else {
        $val2 = "Open";
        $resclass = "";
    }
    if (isset($_POST["submit"])) {
        $sql = "DELETE FROM questions WHERE q_id='$q_id';";
        if (mysqli_query($connect, $sql)) {
            $class = "d-none";
            echo "Success!";
            header("Refresh:1; url=../users/" . $address);
        } else {
            echo "Error!";
        }
    }
}

if (isset($_GET["a_id"])) {
    $a_id = $_GET["a_id"];
    $sql = "SELECT * FROM answers WHERE a_id='$a_id';";
    $res = mysqli_query($connect, $sql);
    $data = mysqli_fetch_assoc($res);
    $q_id = $data["fk_q_id"];
    if (isset($_SESSION["user"])) {
        if ((!$data["fk_u_id"]) || ($u_id !== $data["fk_u_id"])) {
            header("Location: view-question.php?id=" . $q_id);
            exit;
        }
    }
    $sql = "DELETE FROM answers WHERE a_id='$a_id';";
    if (mysqli_query($connect, $sql)) {
        header("Location: view-question.php?id=" . $q_id);
    } else {
        echo "Error!";
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
        <h6>Tags: <?php echo $tag; ?></h6>
        <hr>
        <form method="post" enctype="multipart/form-data">
            <button name="submit" class="btn btn-danger">Proceed</button>
            <a href="../users/<?php echo $address; ?>" class="btn btn-warning">No, go back</a>
        </form>

    </div>
</body>

</html>