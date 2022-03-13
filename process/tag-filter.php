<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
require_once "../components/db_connect.php";
require_once "../components/functions.php";
$u_id = sessFct();
function insertText($tag)
{
    if (empty($tag)) {
        return "1";
    } else {
        return "q_id IN(SELECT q_id FROM (tags JOIN quetag ON tags.t_id=quetag.fk_t_id JOIN questions ON quetag.fk_q_id=questions.q_id) WHERE title='$tag')";
    }
}
$questionbdy = "";
if ((isset($_POST["tag1"])) && (isset($_POST["tag2"])) && (isset($_POST["tag3"]))) {
    $txt1 = insertText($_POST["tag1"]);
    $txt2 = insertText($_POST["tag2"]);
    $txt3 = insertText($_POST["tag3"]);
    $tag1 = $_POST["tag1"];
    $tag2 = $_POST["tag2"];
    $tag3 = $_POST["tag3"];
    if ((empty($tag1)) && (empty($tag2)) && (empty($tag3))) {
        echo $questionbdy;
    } else {
        $sql = "SELECT q_id,q_title,q_status,q_resolved,q_date,q_vote,u_id,l_name FROM (tags JOIN quetag ON tags.t_id=quetag.fk_t_id JOIN questions ON quetag.fk_q_id=questions.q_id) LEFT JOIN users on questions.fk_u_id=u_id WHERE " . $txt1 . " AND " . $txt2 . " AND " . $txt3 . " GROUP by q_id;";
        $res = mysqli_query($connect, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            $q_id = $row["q_id"];
            $query = "SELECT title, t_id FROM tags JOIN quetag ON tags.t_id=quetag.fk_t_id WHERE quetag.fk_q_id='$q_id' ORDER BY t_id ASC;";
            $tagbdy = "";
            $result = mysqli_query($connect, $query);
            while ($line = mysqli_fetch_assoc($result)) {
                $tagbdy .= "<span>" . $line["title"] . "</span>";
            }
            if ($row["u_id"]) {
                $val = $row["l_name"] . "-" . $row["u_id"];
            } else {
                $val = "Fromer user";
            }
            if ($row["q_resolved"] == 1) {
                $val2 = "Answered";
                $resclass = "resolved";
            } else {
                $val2 = "Open";
                $resclass = "";
            }
            if ((isset($_SESSION["adm"])) || ($u_id == $row["u_id"])) {
                $questionbdy .= "<div class='mz-question'>
        <h3>" . $row["q_title"] . "</h3>
        <h4>Tags: " . $tagbdy . "</h4>
        <h5>" . $row["q_vote"] . "</h5>
        <h5 class='" . $resclass . "'>" . $val2 . "</h5>
        <h5>By " . $val . "</h5>
        <a href='view-question.php?id=" . $row["q_id"] . "' class='btn btn-primary'>View</a>
        <a href='edit-question.php?q_id=" . $row["q_id"] . "' class='btn btn-warning'>Edit</a>
        <a href='delete.php?q_id=" . $row["q_id"] . "' class='btn btn-danger'>Delete</a>
<hr>
        </div>";
            } else {


                $questionbdy .= "<div class='mz-question'>
        <h3>" . $row["q_title"] . "</h3>
        <h4>Tags: " . $tagbdy . "</h4>
        <h5>" . $row["q_vote"] . "</h5>
        <h5 class='" . $resclass . "'>" . $val2 . "</h5>
        <h5>By " . $val . "</h5>
        <a href='view-question.php?id=" . $row["q_id"] . "' class='btn btn-primary'>View</a>
<hr>
        </div>";
            }
        }
        echo $questionbdy;
    }
}
