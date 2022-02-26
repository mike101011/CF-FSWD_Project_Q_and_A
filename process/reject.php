<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
if (!isset($_GET["a_id"])) {
    header("Location: ../index.php");
    exit;
} else {
    require_once "../components/db_connect.php";
    $a_id = $_GET["a_id"];
    if (isset($_SESSION["adm"])) {
        $u_id = $_SESSION["adm"];
    } else {
        $u_id = $_SESSION["user"];
    }
    $error = false;
    $sql = "SELECT answers.a_id,answers.a_resolve,answers.fk_u_id AS usans,questions.q_id,questions.fk_u_id AS usques FROM answers JOIN questions ON answers.fk_q_id=questions.q_id WHERE answers.a_id='$a_id';  ";
    $res = mysqli_query($connect, $sql);
    $data = mysqli_fetch_assoc($res);
    $asker = $data["usques"];
    $answ = $data["usans"];
    $q_id = $data["q_id"];
    $a_resolve = $data["a_resolve"];
    if ($u_id !== $asker) {
        $error = true;
        header("Location: ../questions/view-question.php?id=" . $data["q_id"] . "");
        exit;
    }
    if ($a_resolve == 0) {
        $error = true;
        header("Location: ../questions/view-question.php?id=" . $data["q_id"] . "");
        exit;
    }
    if (!$error) {
        $sql2 = "UPDATE answers SET a_resolve='0' WHERE a_id='$a_id';";
        if (mysqli_query($connect, $sql2)) {
            $sql3 = "SELECT * FROM users WHERE u_id='$answ';";
            $res3 = mysqli_query($connect, $sql3);
            $data2 = mysqli_fetch_assoc($res3);
            $points = $data2["points"];
            $points--;
            $sql4 = "UPDATE users SET points='$points' WHERE u_id='$answ';";
            if (mysqli_query($connect, $sql4)) {
                $sql5 = "UPDATE questions SET q_resolved='0' WHERE q_id='$q_id';";
                if (mysqli_query($connect, $sql5)) {
                    header("Location: ../questions/view-question.php?id=" . $data["q_id"] . "");
                }
            }
        }
    }
    mysqli_close($connect);
}
