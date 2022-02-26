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
    if (isset($_SESSION["adm"])) {
        $u_id = $_SESSION["adm"];
    } else {
        $u_id = $_SESSION["user"];
    }
    $likeclass = "";
    $querry = "SELECT * FROM vote_details WHERE fk_q_id='$q_id' AND fk_u_id='$u_id';";
    $check = mysqli_query($connect, $querry);
    if (mysqli_num_rows($check) > 0) {
        $likeclass = "d-none";
    }
    $sql1 = "SELECT * FROM (tags JOIN quetag ON tags.t_id=quetag.fk_t_id JOIN questions ON quetag.fk_q_id=questions.q_id) LEFT JOIN users ON questions.fk_u_id=users.u_id WHERE q_id='$q_id'; ";
    $res1 = mysqli_query($connect, $sql1);
    $data1 = mysqli_fetch_assoc($res1);
    $title = $data1["q_title"];
    $q_txt = $data1["q_txt"];
    $q_date = $data1["q_date"];
    $q_vote = $data1["q_vote"];
    if ($data1["fk_u_id"]) {
        $val = $data1["l_name"] . "-" . $data1["fk_u_id"];
        if ($data1["fk_u_id"] == $u_id) {
            $likeclass = "d-none";
        }
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
    $standbdy = $answbdy = "";
    $querry2 = "SELECT * FROM questions WHERE q_id='$q_id';";
    $check2 = mysqli_query($connect, $querry2);
    $result = mysqli_fetch_assoc($check2);
    $sql2 = "SELECT * FROM answers LEFT JOIN users on answers.fk_u_id=u_id WHERE answers.fk_q_id='$q_id' AND a_resolve='0' ORDER BY a_id DESC;";
    $res2 = mysqli_query($connect, $sql2);
    if ($res2->num_rows > 0) {
        $standardclass = "";
        $standbdy = "<h4>Comments</h4>";
        while ($row = mysqli_fetch_assoc($res2)) {
            if ($row["fk_u_id"]) {
                $val3 = $row["l_name"] . "-" . $row["fk_u_id"];
            } else {
                $val3 = "Fromer user";
            }

            if ($result["fk_u_id"] == $u_id) {
                $standbdy .= "<div>
                <div class='txt'>" . $row["a_text"] . "</div>
                <div class='txt-info'>
                    <h6>By " . $val3 . "</h6> 
                    <p>Posted on " . $row["a_date"] . "</p>
                </div>
                <a href='../process/accept.php?a_id=" . $row["a_id"] . "' class='btn btn-success'>Accept</a>
            </div>";
            } else {
                $standbdy .= "<div>
                <div class='txt'>" . $row["a_text"] . "</div>
                <div class='txt-info'>
                    <h6>By " . $val3 . "</h6> 
                    <p>Posted on " . $row["a_date"] . "</p>
                </div>
            </div>";
            }
        }
    }
    $sql3 = "SELECT * FROM answers LEFT JOIN users on answers.fk_u_id=u_id WHERE answers.fk_q_id='$q_id' AND a_resolve='1' ORDER BY a_id DESC;";
    $res3 = mysqli_query($connect, $sql3);
    if ($res3->num_rows > 0) {
        $answerclass = "";
        $answbdy = "<h4>Answers</h4>";
        while ($row3 = mysqli_fetch_assoc($res3)) {
            if ($row3["fk_u_id"]) {
                $val4 = $row3["l_name"] . "-" . $row3["fk_u_id"];
            } else {
                $val4 = "Fromer user";
            }
            if ($result["fk_u_id"] == $u_id) {
                $answbdy
                    .= "<div>
                <div class='txt'>" . $row3["a_text"] . "</div>
                <div class='txt-info'>
                    <h6>By " . $val4 . "</h6> 
                    <p>Posted on " . $row3["a_date"] . "</p>
                </div>
                <a href='../process/reject.php?a_id=" . $row3["a_id"] . "' class='btn btn-warning'>Reject</a>
            </div>";
            } else {
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
    <style>
        #comments {
            max-height: 15rem;
            overflow-y: scroll;
            margin-bottom: 1em;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="question-top">
            <h5 class="text-center"><?php echo $title; ?></h5>
            <span class="<?php echo $resclass; ?>"><?php echo $val2; ?></span>
        </div>
        <div>
            <h5 class="text-right q-vote">Votes: <?php echo $q_vote; ?></h5>
            <?php echo $q_txt; ?>
        </div>
        <div class="question-bottom">
            <h6>Posted by <?php echo $val; ?></h6>
            <p>Date: <?php echo $q_date; ?></p>
            <button id="like-btn" class="btn btn-primary <?php echo $likeclass; ?>">Like</button>
            <button id="dislike-btn" class="btn btn-secondary <?php echo $likeclass; ?>">Dislike</button>
        </div>
        <hr>
        <div class="mz-answers">
            <?php echo $answbdy; ?>
        </div>

        <div id="comments"><?php echo $standbdy; ?></div>

        <hr>
        <div>
            <h4>Post Comment</h4>
            <form id="comment-form" method="post" enctype="multipart/form-data">
                <fieldset>
                    <div>
                        <textarea id="comment-area" name="a_text" cols="40" rows="15" placeholder="Comment here"></textarea>
                    </div>
                    <input type="hidden" id="question" name="q_id" value="<?php echo $q_id; ?>">
                    <button type="submit" class="btn btn-secondary">Post</button>

                </fieldset>
            </form>

        </div>
    </div>
    </div>

    <script>
        let likebtn = document.getElementById("like-btn");
        likebtn.addEventListener("click", likequest);

        function likequest() {
            let q_id = <?php echo $q_id; ?>;
            let params = `val=1&&q_id=${q_id}`;
            let request = new XMLHttpRequest();
            request.open("POST", "../process/like.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.onload = function() {
                if (this.status == 200) {
                    let val = this.responseText;
                    if (val !== "") {
                        document.getElementsByClassName("q-vote")[0].innerHTML = "Votes: " + val;
                    }
                    dislikebtn.style.display = "none";
                    likebtn.style.display = "none";
                }
            }
            request.send(params);
        }
        let dislikebtn = document.getElementById("dislike-btn");
        dislikebtn.addEventListener("click", disquest);

        function disquest() {
            let q_id = <?php echo $q_id; ?>;
            let params = `val=-1&&q_id=${q_id}`;
            let request = new XMLHttpRequest();
            request.open("POST", "../process/like.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.onload = function() {
                if (this.status == 200) {
                    let val = this.responseText;
                    if (val !== "") {
                        document.getElementsByClassName("q-vote")[0].innerHTML = "Votes: " + val;
                    }
                    likebtn.style.display = "none";
                    dislikebtn.style.display = "none";
                }
            }
            request.send(params);
        }
        let form = document.getElementById("comment-form");
        form.addEventListener("submit", commentfct);

        function commentfct(e) {
            e.preventDefault();
            let a_text = document.getElementById("comment-area").value;
            let q_id = document.getElementById("question").value;
            if (a_text == "") {
                alert("No comment written.");
            } else {
                let params = `a_text=${a_text}&&q_id=${q_id}`;
                let request = new XMLHttpRequest();
                request.open("POST", "../process/comment.php", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.onload = function() {
                    if (this.status == 200) {
                        document.getElementById("comments").innerHTML = this.responseText;


                    }
                }
                request.send(params);
                document.getElementById("comment-form").reset();
            }


        }
    </script>

</body>

</html>