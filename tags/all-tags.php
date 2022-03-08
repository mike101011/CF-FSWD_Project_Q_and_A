<?php
session_start();
if ((!isset($_SESSION["adm"])) && (!isset($_SESSION["user"]))) {
    header("Location: ../index.php");
    exit;
}
require_once "../components/db_connect.php";
$linkclass = "d-none";
if (isset($_SESSION["adm"])) {
    $linkclass = "";
}
$sql = "SELECT * FROM tags;";
$res = mysqli_query($connect, $sql);
$tagbody = "";
while ($row = mysqli_fetch_assoc($res)) {
    if ($row["t_description"]) {
        $text = $row["t_description"];
    } else {
        $text = "No description.";
    }
    $tagbody .= "<div class='col-12 col-md-6 col-lg-3'>
            <div class='card'>
            <div class='card-header'>" . $row["title"] . "
            </div>
            <div class='card-body'>
                <blockquote class='blockquote mb-0'>
                    " . $text . "
                </blockquote>
                <div><a href='edit.php?t_id=" . $row["t_id"] . "' class='btn btn-warning " . $linkclass . "'>Edit</a></div>
            </div>
        </div>
        </div>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tags</title>
    <?php require_once "../components/boot-css.php"; ?>
</head>

<body>
    <nav class="<?php echo $linkclass; ?>">
        <div>
            <a href="create.php" class="btn btn-secondary">New Tag</a>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <?php echo $tagbody; ?>
        </div>


    </div>

</body>

</html>