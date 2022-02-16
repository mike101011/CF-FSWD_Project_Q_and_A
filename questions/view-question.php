<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
if (!isset($_GET["id"])) {
    header("Location: ../index.php");
    exit;
} else {
    require_once "../components/db_connect.php";
    $q_id = $_GET["id"];
    $sql1 = "SELECT * FROM (tags JOIN quetag ON tags.t_id=quetag.fk_t_id JOIN questions ON quetag.fk_q_id=questions.q_id) LEFT JOIN users ON questions.fk_u_id=users.u_id WHERE q_id='$q_id'; ";
    $res1 = mysqli_query($connect, $sql1);
    $data1 = mysqli_fetch_assoc($res1);
    $title = $data1["q_title"];
    $q_txt = $data1["q_txt"];
    $q_date = $data1["q_date"];
    $q_vote = $data1["q_vote"];
    if ($data1["fk_u_id"]) {
        $val = $data1["l_name"] . "-" . $data1["fk_u_id"];
    } else {
        $val = "Fromer user";
    }
    if ($data1["q_resolved"] == 1) {
        $val2 = "Answered";
        $resclass = "resolved";
    } else {
        $val2 = "Open";
        $resclass = "";
    }
    $standardclass = $answerclass = "d-none";
    $standbdy = $answbdy = "";
    $sql2 = "SELECT * FROM answers LEFT JOIN users on answers.fk_u_id=u_id WHERE answers.fk_q_id='$q_id' AND a_resolve='0';";
    $res2 = mysqli_query($connect, $sql2);
    if ($res2->num_rows > 0) {
        $standardclass = "";
        while ($row = mysqli_fetch_assoc($res2)) {
            if ($row["fk_u_id"]) {
                $val3 = $row["l_name"] . "-" . $row["fk_u_id"];
            } else {
                $val3 = "Fromer user";
            }
            $standbdy .= "<div>
                <div class='txt'>" . $row["a_text"] . "</div>
                <div class='txt-info'>
                    <h6>By " . $val3 . "</h6> 
                    <p>Posted on " . $row["a_date"] . "</p>
                </div>
            </div>";
        }
    }
    $sql3 = "SELECT * FROM answers LEFT JOIN users on answers.fk_u_id=u_id WHERE answers.fk_q_id='$q_id' AND a_resolve='1';";
    $res3 = mysqli_query($connect, $sql3);
    if ($res3->num_rows > 0) {
        $answerclass = "";
        while ($row3 = mysqli_fetch_assoc($res3)) {
            if ($row3["fk_u_id"]) {
                $val4 = $row3["l_name"] . "-" . $row3["fk_u_id"];
            } else {
                $val4 = "Fromer user";
            }
            $answbdy
                .= "<div>
                <div class='txt'>" . $row3["a_text"] . "</div>
                <div class='txt-info'>
                    <h6>By " . $val4 . "</h6> 
                    <p>Posted on " . $row3["a_date"] . "</p>
                </div>
            </div>";
        }
    }
    $error = false;
    $textError = "";
    if (isset($_POST["submit"])) {
        $a_text = $_POST["answer"];
        $q_id = $_GET["id"];
        if (isset($_SESSION["adm"])) {
            $u_id = $_SESSION["adm"];
        } else {
            $u_id = $_SESSION["user"];
        }
        $date = date("Y.m.d");
        if (empty($a_text)) {
            $error = true;
            $textError = "No comment written.";
        }
        if (!$error) {
            $querry = "INSERT INTO answers(a_text, fk_q_id,fk_u_id,a_date) VALUES('$a_text','$q_id','$u_id','$date');";
            if (mysqli_query($connect, $querry)) {
                echo "  SUCCESS!!";
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
    <title>Question Details</title>
    <?php require_once "../components/boot-css.php"; ?>
</head>

<body>
    <div class="container">
        <div class="question-top">
            <h5 class="text-center"><?php echo $title; ?></h5>
            <span class="<?php echo $resclass; ?>"><?php echo $val2; ?></span>
        </div>
        <div>
            <h5 class="text-right">Votes: <?php echo $q_vote; ?></h5>
            <?php echo $q_txt; ?>
        </div>
        <div class="question-bottom">
            <h6>Posted by <?php echo $val; ?></h6>
            <p>Date: <?php echo $q_date; ?></p>
        </div>
        <hr>
        <div class="mz-answers">
            <div class="<?php echo $answerclass; ?>">
                <h4>Answers</h4>
                <?php echo $answbdy; ?>
            </div>
            <div class="<?php echo $standardclass; ?>">
                <h4>Comments</h4>
                <?php echo $standbdy; ?>
            </div>
            <div>
                <h4>Post Comment</h4>
                <form method="post" enctype="multipart/form-data">
                    <fieldset>
                        <div>
                            <textarea name="answer" cols="40" rows="15" placeholder="Comment here"></textarea>
                            <span class="text-danger"><?php echo $textError; ?></span>
                        </div>
                        <button type="submit" name="submit" class="btn btn-secondary">Post</button>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>

</body>

</html>