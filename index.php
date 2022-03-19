<?php
session_start();
if (isset($_SESSION['user']) != "") {
    header("Location: users/user-home.php"); // redirects to home.php
}
if (isset($_SESSION['adm']) != "") {
    header("Location: users/dashboard.php"); // redirects to home.php
}
require_once "components/db_connect.php";
require_once "components/file_upload.php";
require_once "components/functions.php";
$email = $pass = $emailError = "";
$errclass = "d-none";
$error = false;
if (isset($_POST["submit-btn"])) {
    $email = inpTransf($_POST["email"]);
    $pass = inpTransf($_POST["pass"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $emailError = "Please enter valid email address.";
    } else {
        $pass = hash('sha256', $pass);
        $sql = "SELECT * FROM users WHERE email='$email' AND pass='$pass'";
        $res = mysqli_query($connect, $sql);
        if (mysqli_num_rows($res) == 0) {
            $error = true;
            $errclass = "";
        }
        if (!$error) {
            $row = mysqli_fetch_assoc($res);
            if ($row["status"] == "banned") {
                $_SESSION["banned"] = $row["u_id"];
                header("Location: users/block.php");
                mysqli_close($connect);
                exit;
            }
            checkQuestions($connect);
            if ($row["role"] == "adm") {
                $_SESSION["adm"] = $row["u_id"];
                header("Location: users/dashboard.php");
                mysqli_close($connect);
                exit;
            } else {
                $_SESSION["user"] = $row["u_id"];
                header("Location: users/user-home.php");
            }
        }
    }
}
mysqli_close($connect);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programming Q and A</title>
    <link rel="stylesheet" href="https://unpkg.com/open-props" />
    <link rel="stylesheet" href="https://unpkg.com/open-props/normalize.min.css" />
    <?php require_once "components/boot-css.php" ?>
    <style>
        <?php require_once "styles/users-styles.css" ?>
    </style>

</head>

<body id="index">
    <header class="page-header">
        <div class="container flow">
            <h1 class="text-center page-title">Welcome to Programming Q and A!</h1>
            <p class="page-subtitle"></p>
        </div>
    </header>
    <h2 class="section-title"></h2>
    <div class="media-scroller">
        <div class="media-element"><img src="https://images.pexels.com/photos/10845119/pexels-photo-10845119.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="Error">
            <p class="title">Short title</p>
        </div>
        <div class="media-element"><img src="https://images.pexels.com/photos/4974912/pexels-photo-4974912.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260" alt="Error">
            <p class="title">Second picture</p>
        </div>
        <div class="media-element"><img src="https://logos-world.net/wp-content/uploads/2021/10/Python-Symbol.png" alt="Error">
            <p class="title">Third picture</p>
        </div>
        <div class="media-element"></div>
    </div>

    <div class="container">
        <div class="<?php echo $errclass; ?> text-danger">
            <h6>Wrong credentials! Try again.</h6>
        </div>
        <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off" enctype="multipart/form-data">
            <div class="email"> <input type="text" class="form-control" name="email" placeholder="Email">
            </div>
            <span class="text-danger"><?php echo $emailError; ?></span>
            <div class="password"><input type="password" class="form-control" name="pass" placeholder="Password"></div>
            <button type="submit" name="submit-btn" class="btn btn-secondary">Sign in!</button> <span>or register </span><a href="users/register.php">Here</a>

        </form>
    </div>
    <script>
        function fib(n) {
            if ((n == 0) || (n == 1)) {
                return 1;
            } else {
                let counter_0 = 1;
                let counter_1 = 1;
                let v = 1;
                let res = 1;
                while (v < n) {
                    res = counter_0 + counter_1;
                    counter_0 = counter_1;
                    counter_1 = res;
                    v++;
                }
                return res;
            }
        }

        function grid(row, col, mem = {}) {
            if ((row == 0) || (col == 0)) {
                return 0;
            } else if ((row == 1) || (col == 1)) {
                return 1;
            } else {

                if (`(${row},${col})` in mem) {
                    return mem[`(${row},${col})`];
                } else {
                    mem[`(${row},${col})`] = grid(row - 1, col, mem) + grid(row, col - 1, mem);
                    return mem[`(${row},${col})`];
                }
            }
        }

        function grid2(row, col) {
            if ((row == 0) || (col == 0)) {
                return 0;
            } else if ((row == 1) || (col == 1)) {
                return 1;
            } else {
                return grid2(row - 1, col) + grid2(row, col - 1);
            }
        }

        function fact(n) {
            if (n == 0) {
                return 1;
            } else {
                return n * fact(n - 1);
            }
        }

        function grid3(row, col) {
            if ((row == 0) || (col == 0)) {
                return 0;
            } else if ((row == 1) || (col == 1)) {
                return 1;
            } else {
                return fact(row + col - 2) / (fact(row - 1) * fact(col - 1));
            }
        }

        function palin(strg) {
            if (strg == "") {
                return true;
            } else {
                const regex = /[^A-Za-z0-9]/g;
                let temp1 = strg.replace(regex, "");
                let txt1 = temp1.toLowerCase();
                let leng = txt1.length;
                let txt2 = "";
                for (let i = 0; i < leng; i++) {
                    txt2 += txt1[leng - (1 + i)];
                }
                if (txt1 == txt2) {
                    return true;
                } else {
                    return false;
                }
            }

        }

        function fib2(n, mem = {}) {
            if (n <= 1) {
                return 1;
            } else {
                if (`${n}` in mem) {
                    return mem[`${n}`];
                } else {
                    mem[`${n}`] = fib2(n - 1, mem) + fib2(n - 2, mem);
                    return mem[`${n}`];
                }
            }
        }
    </script>

</body>

</html>