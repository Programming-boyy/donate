<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    require_once "database.php";

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $full_name, $email, $hashed_password);
        mysqli_stmt_execute($stmt);
        header("Location: login.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error creating account.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

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
        <h2 align="right">انشاء حساب</h2>
        <form action="registration.php" method="post">
            <div class="mb-3">
                <input type="text" name="full_name" id="full_name" placeholder="أدخل اسمك" class="form-control"  required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" id="email" placeholder=" أدخل الايميل الخاص بك" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" id="password" placeholder="أدخل كلمة المرور" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <a href="login.php" class="btn btn-link mt-2">Already have an account? Login here.</a>
    </div>
</body>
</html>