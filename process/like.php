<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
require_once "../components/db_connect.php";
if (isset($_SESSION["adm"])) {
    $u_id = $_SESSION["adm"];
} else {
    $u_id = $_SESSION["user"];
}
$error = false;
if ((isset($_POST["val"])) && (isset($_POST["q_id"]))) {
    $q_id
        = mysqli_real_escape_string($connect, $_POST["q_id"]);
    $val
        = mysqli_real_escape_string($connect, $_POST['val']);
    $querry = "SELECT * FROM vote_details WHERE fk_q_id='$q_id' AND fk_u_id='$u_id';";
    $check = mysqli_query($connect, $querry);
    if (mysqli_num_rows($check) > 0) {
        $error = true;
    }
    $querry2 = "SELECT * FROM questions WHERE q_id='$q_id';";
    $check2 = mysqli_query($connect, $querry2);
    $result = mysqli_fetch_assoc($check2);
    if ($result["fk_u_id"] == $u_id) {
        $error = true;
    }
    if (!$error) {
        $sql3 = "INSERT INTO vote_details(fk_q_id,fk_u_id,val) VALUES('$q_id','$u_id','$val');";
        if (mysqli_query($connect, $sql3)) {
            $sql_1 = "SELECT * FROM questions WHERE q_id='$q_id';";
            $res1 = mysqli_query($connect, $sql_1);
            $data = mysqli_fetch_assoc($res1);
            $author = $data["fk_u_id"];
            if ($val == "1") {
                $sql4 = "SELECT * FROM users WHERE u_id='$author';";
                $res4 = mysqli_query($connect, $sql4);
                $data2 = mysqli_fetch_assoc($res4);
                $points = $data2["points"];
                $points++;
                $sql5 = "UPDATE users SET points='$points' WHERE u_id='$author';";
                mysqli_query($connect, $sql5);
            }
            $val = $val + $data["q_vote"];
            $sql2 = "UPDATE `questions` SET `q_vote` = '$val' WHERE q_id = '$q_id'; ";
            if (mysqli_query($connect, $sql2)) {
                echo $val;
            }
        }
    }
}
mysqli_close($connect);
