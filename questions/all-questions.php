<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
require_once "../components/db_connect.php";
require_once "../components/functions.php";
$u_id = sessFct();
$questionbdy = $msg = "";
$quesall = "";
$quesdet = "d-none";
$class = "d-none";
$sql = "SELECT * FROM questions LEFT JOIN users ON questions.fk_u_id=users.u_id ORDER BY q_id DESC; ";
$res = mysqli_query($connect, $sql);
if ($res->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $id_val = $row["q_id"];
        $tagbdy = "";
        $query = "SELECT title FROM tags JOIN quetag ON tags.t_id=quetag.fk_t_id WHERE fk_q_id='$id_val' ORDER BY t_id ASC; ";
        $result = mysqli_query($connect, $query);
        while ($line = mysqli_fetch_assoc($result)) {
            $tagbdy .= "<span>" . $line["title"] . "</span>";
        }
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
        if ((isset($_SESSION["adm"])) || ($u_id == $row["fk_u_id"])) {
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
} else {
    $class = "";
    $msg = "No question posted.";
}
if (isset($_GET["user"])) {
    $u_id = $_GET["user"];
    if ($u_id !== sessFct()) {
        header("Location: ../index.php");
        exit;
    } else {
        $quesall = "d-none";
        $quesdet = "";
        $class = "d-none";
        $questionbdy = $msg = "";
        $sql = "SELECT * FROM questions LEFT JOIN users ON questions.fk_u_id=users.u_id WHERE u_id='$u_id' ORDER BY q_id DESC; ";
        $res = mysqli_query($connect, $sql);
        if ($res->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $id_val = $row["q_id"];
                $tagbdy = "";
                $query = "SELECT * FROM tags JOIN quetag ON tags.t_id=quetag.fk_t_id JOIN questions ON quetag.fk_q_id=questions.q_id WHERE q_id='$id_val';";
                $result = mysqli_query($connect, $query);
                while ($line = mysqli_fetch_assoc($result)) {
                    $tagbdy .= "<span>" . $line["title"] . "</span>";
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
        <h4>Tags: " . $tagbdy . "</h4>
        <h5>" . $row["q_vote"] . "</h5>
        <h5 class='" . $resclass . "'>" . $val2 . "</h5>
        <a href='view-question.php?id=" . $row["q_id"] . "' class='btn btn-primary'>View</a>
        <a href='edit-question.php?q_id=" . $row["q_id"] . "' class='btn btn-warning'>Edit</a>
<hr>
        </div>";
            }
        } else {
            $class = "";
            $msg = "No question posted.";
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
    <a href="all-questions.php?user=<?php echo $u_id; ?>" class="btn btn-info <?php echo $quesall; ?>">Your Questions</a>
    <div class="container <?php echo $quesall; ?>">
        <h1 class="text-center">View Questions</h1>
        <div class="<?php echo $class; ?>">
            <h3 class="text-center"><?php echo $msg; ?></h3>
        </div>
        <div>
            <form id="tag-filter" method="post" enctype="multipart/form-data">
                <table class="table">
                    <tr>
                        <th>Tag 1</th>
                        <td><input type="text" id="tag1" placeholder="Tag 1"></td>
                    </tr>
                    <tr>
                        <th>Tag 2</th>
                        <td><input type="text" id="tag2" placeholder="Tag 2"></td>
                    </tr>
                    <tr>
                        <th>Tag 3</th>
                        <td><input type="text" id="tag3" placeholder="Tag 3"></td>
                    </tr>
                </table>
                <div>
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </form>
        </div>
        <div id="questions">
            <?php echo $questionbdy; ?>
        </div>
    </div>

    </div>
    <div class="container <?php echo $quesdet; ?>">
        <h1 class="text-center">Your Questions</h1>
        <div class="<?php echo $class; ?>">
            <h3 class="text-center"><?php echo $msg; ?></h3>
        </div>

        <script>
            let frm = document.getElementById("tag-filter");
            frm.addEventListener("submit", filterfct);

            function filterfct(e) {
                e.preventDefault();

                let tag1 = document.getElementById("tag1").value;
                let tag2 = document.getElementById("tag2").value;
                let tag3 = document.getElementById("tag3").value;
                let params = `tag1=${tag1}&&tag2=${tag2}&&tag3=${tag3}`;
                let request = new XMLHttpRequest();
                request.open("POST", "../process/tag-filter.php");
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.onload = function() {
                    if (this.status == 200) {
                        document.getElementById("questions").innerHTML = this.responseText;
                    }
                }
                request.send(params);
            }
        </script>
</body>

</html>