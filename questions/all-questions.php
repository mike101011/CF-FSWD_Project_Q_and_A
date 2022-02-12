<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
require_once "../components/db_connect.php";
$questionbdy = $msg = "";
$class = "d-none";
$sql = "SELECT * FROM (tags JOIN quetag ON tags.t_id=quetag.fk_t_id JOIN questions ON quetag.fk_q_id=questions.q_id) LEFT JOIN users ON questions.fk_u_id=users.u_id; ";
$res = mysqli_query($connect, $sql);
if ($res->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row["fk_u_id"]) {
            $val = $row["l_name"] . "-" . $row["fk_u_id"];
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

        $questionbdy .= "<div class='mz-question'>
        <h3>" . $row["q_title"] . "</h3>
        <h4>Tag: " . $row["title"] . "</h4>
        <h5>" . $row["q_vote"] . "</h5>
        <h5 class='" . $resclass . "'>" . $val2 . "</h5>
        <h5>By " . $val . "</h5>
        <a href='view-question.php?id=" . $row["q_id"] . "' class='btn btn-primary'>View</a>
<hr>
        </div>";
    }
} else {
    $class = "";
    $msg = "No question posted.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions</title>
    <?php require_once "../components/boot-css.php" ?>
    <style>
        .mz-question {
            display: flex;
            justify-content: space-between;
        }

        .resolved {
            color: green;
        }
    </style>
</head>

<body>
    <h1 class="text-center">View Questions</h1>
    <div class="container">
        <div class="<?php echo $class; ?>">
            <h3 class="text-center"><?php echo $msg; ?></h3>
        </div>
        <div>
            <?php echo $questionbdy; ?>
        </div>

    </div>
</body>

</html>