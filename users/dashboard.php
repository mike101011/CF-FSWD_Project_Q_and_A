<?php
session_start();
if ((!isset($_SESSION["user"])) && (!isset($_SESSION["adm"]))) {
    header("Location: ../index.php");
}
if (isset($_SESSION["user"])) {
    header("Location: user-home.php");
}
require_once "../components/db_connect.php";
if (isset($_SESSION["adm"])) {
    $id = $_SESSION["adm"];
    $sql = "SELECT * FROM users WHERE u_id='$id';";
    $res = mysqli_query($connect, $sql);
    $data = mysqli_fetch_assoc($res);
    $fname = $data["f_name"];
    $lname = $data["l_name"];
    $pic = $data["picture"];
    $userbdy = "";
    $userclass = $postclass = "d-none";
    if (isset($_GET["Users"])) {
        $userclass = "";
        $quer = "SELECT * FROM users WHERE role='user';";
        $result = mysqli_query($connect, $quer);
        if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $userbdy .= "<tr>
                        <th scope='row'>" . $row["u_id"] . "</th>
                        <td>" . $row["f_name"] . "</td>
                        <td>" . $row["l_name"] . "</td>
                        <td>" . $row["b_date"] . "</td>
                        <td>" . $row["status"] . "</td>
                        <td><a href='edit.php?id=" . $row["u_id"] . "' class='btn btn-info'>Edit</a> <a href='read.php?id=" . $row["u_id"] . "' class='btn btn-success'>Info</a> <a href='delete.php?id=" . $row["u_id"] . "' class='btn btn-danger'>Delete</a></td>
                    </tr>";
            }
        }
    }
    if (isset($_GET["posts"])) {
        $postclass = "";
        $postbdy = "";
        $sql = "SELECT l_name, u_id FROM users;";
        $res = mysqli_query($connect, $sql);
        $data = $res->fetch_all(MYSQLI_ASSOC);
        for ($i = 0; $i < count($data); $i++) {
            $u_id = $data[$i]["u_id"];
            $query = "SELECT count(fk_u_id) AS ques FROM questions WHERE fk_u_id='$u_id';";
            $result = mysqli_query($connect, $query);
            $row = mysqli_fetch_assoc($result);
            $questions = $row["ques"];
            $query = "SELECT count(fk_u_id) AS comts FROM answers WHERE fk_u_id='$u_id';";
            $result = mysqli_query($connect, $query);
            $row = mysqli_fetch_assoc($result);
            $comments = $row["comts"];
            $query = "SELECT count(fk_u_id) AS ans FROM answers WHERE fk_u_id='$u_id' AND a_resolve='1';";
            $result = mysqli_query($connect, $query);
            $row = mysqli_fetch_assoc($result);
            $answers = $row["ans"];
            $postbdy .= "<tr>
                        <th scope='row'>" . $data[$i]["l_name"] . "-" . $data[$i]["u_id"] . "</th>
                        <td>" . $questions . "</td>
                        <td>" . $comments . "</td>
                        <td>" . $answers . "</td>
                        
                        </tr>";
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
    <title>Document</title>
    <?php require_once "../components/boot-css.php" ?>
</head>

<body>
    <div class="container">
        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-info">Edit your profile</a>
        <a href="../questions/post-question.php" class="btn btn-warning">Post a question</a>
        <a href="../questions/all-questions.php" class="btn btn-info">Questions</a>
        <a href="../tags/all-tags.php" class="btn btn-secondary">Tags</a>
        <a href="logout.php" class="btn btn-secondary">Log out</a>
        <h5>Hello, <?php echo $fname . " " . $lname; ?>!</h5>
        <img src="../pictures/<?php echo $pic; ?>" alt="">
        <a href="dashboard.php?Users" class="btn btn-warning">See Users</a>
        <a href="dashboard.php?posts" class="btn btn-info">Post Statistics</a>
        <div class="<?php echo $userclass; ?>">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Birth Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $userbdy; ?>
                </tbody>
            </table>
        </div>
        <div class="<?php echo $postclass; ?>">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">User Name</th>
                        <th scope="col">Questions</th>
                        <th scope="col">Comments</th>
                        <th scope="col">Answers</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $postbdy; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>