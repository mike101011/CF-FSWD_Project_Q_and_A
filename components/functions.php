<?php
function sessFct()
{
    if (isset($_SESSION["adm"])) {
        return $_SESSION["adm"];
    } else {
        return $_SESSION["user"];
    }
}
function checkQuestions($connect)
{
    $date1 = strtotime(date("Y-m-d"));
    $sql = "SELECT q_id, q_date, q_status FROM questions;";
    $res2 = mysqli_query($connect, $sql);
    if ($res2->num_rows > 0) {
        while ($row2 = mysqli_fetch_assoc($res2)) {
            $q_id = $row2["q_id"];
            $date_q = strtotime($row2["q_date"]);
            $sql = "SELECT MAX(a_date) AS ans FROM answers WHERE fk_q_id='$q_id';";
            $result = mysqli_query($connect, $sql);
            $data = mysqli_fetch_assoc($result);
            $date_a = strtotime($data["ans"]);
            $interval_q = ($date1 - $date_q) / (60 * 60 * 24);
            $interval_a = ($date1 - $date_a) / (60 * 60 * 24);
            if (($interval_q >= 3) && ($interval_a >= 3) && ($row2["q_status"] == "active")) {
                $sql = "UPDATE questions SET q_status='inactive' WHERE q_id='$q_id';";
                mysqli_query($connect, $sql);
            }
        }
    }
}
