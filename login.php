<?php
session_start();
require_once "database.php";

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["user"] = $row["full_name"];
            header("Location: index.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Incorrect password.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>No user found with that email.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <title>Login</title>
    <style>
        *{
            font-family: "Rubik", sans-serif;
            padding:0;
            margin:0;
        }
        .container{
            left:50%;
            top:50%;
            position: absolute;
            transform: translate(-50%, -50%);
            box-shadow: 0px 0px 40px #999;
            padding: 30px;
            border-radius: 5px;
        }
        input{
            width:250px;
            height: 20px;
            padding: 6px;
            border: none;
            border-bottom: 2px black solid;
            background:#eeeeee;
            margin-top: 20px;
        }
        input:focus{
            background: #f1f1f1;
            outline: none;
        }
        ::placeholder{
            text-align: right;
            color: #222;
        }
        h2{
            font-size: 35px;
            font-weight: bolder;
        }
        button{
            width: 260px;
            height: 30px;
            border-radius: 5px;
            background: linear-gradient(#111, #333);
            border: none;
            color: #fff;
            margin-top: 10px;
        }
        a{
            font-size: 12px;
            margin-top:15px;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 align="right">تسجيل الدخول</h2>
        <form action="login.php" method="post">
            <div class="mb-3">
                <input type="email" name="email" id="email" placeholder=" أدخل الايميل الخاص بك" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" id="password" placeholder=" أدخل كلمة السر الخاصة بك" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <a href="registration.php" class="btn btn-link mt-2">Don't have an account? Register here.</a>
    </div>
</body>
</html>