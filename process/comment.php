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
$standbdy = "";
if ((isset($_POST["a_text"])) && (isset($_POST["q_id"]))) {
    $q_id = mysqli_real_escape_string($connect, $_POST['q_id']);
    $a_text = mysqli_real_escape_string($connect, $_POST['a_text']);
    $date = date("Y.m.d");
    if (empty($a_text)) {
        $error = true;
    }
    if (!$error) {
        $querry = "INSERT INTO answers(a_text, fk_q_id,fk_u_id,a_date) VALUES('$a_text','$q_id','$u_id','$date');";
        if (mysqli_query($connect, $querry)) {
            $sql = "SELECT * FROM answers LEFT JOIN users on answers.fk_u_id=u_id WHERE answers.fk_q_id='$q_id' AND a_resolve='0' ORDER BY a_id DESC;";
            $res = mysqli_query($connect, $sql);
            if ($res->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    if ($row["fk_u_id"]) {
                        $val = $row["l_name"] . "-" . $row["fk_u_id"];
                    } else {
                        $val = "Fromer user";
                    }
                    $standbdy .= "<div>
                <div class='txt'>" . $row["a_text"] . "</div>
                <div class='txt-info'>
                    <h6>By " . $val . "</h6> 
                    <p>Posted on " . $row["a_date"] . "</p>
                </div>
            </div>";
                }
            }
            echo $standbdy;
        }
    }
}
