<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
require_once "database.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bloodType = $_POST["blood_type"];
    $hospital = $_POST["hospital"];
    $phone = $_POST["phone"];
    $caption = $_POST["caption"];

    if (empty($bloodType) || empty($hospital) || empty($phone) || empty($caption)) {
        $errors[] = "All fields are required.";
    }

    if (strlen($phone) < 10 || !is_numeric($phone)) {
        $errors[] = "Phone number must be at least 10 digits.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO posts (user_name, blood_type, hospital, phone, caption) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $_SESSION["user"], $bloodType, $hospital, $phone, $caption);
    
        if (mysqli_stmt_execute($stmt)) {

            header("Location: index.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
        }
    }
    

}

$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Home Page</title>

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
            left:-2000px;
            top:50%;
            position: fixed;
            transform: translate(-50%, -50%);
            box-shadow: 0px 0px 40px #999;
            padding: 30px;
            border-radius: 5px;
            background: #fff;
            transition: 1s;
        }
        .container h1{
            right:20px;
            top:10px;
            position: absolute;
            transition: 0.50s;
        }
        .container h1:hover{
            color: darkred;
        }
        input{
            width:250px;
            height: 20px;
            padding: 6px;
            border: none;
            border-bottom: 2px black solid;
            background:#eeeeee;
            margin-top: 20px;
            text-align: right;
        }
        input:focus{
            background: #f1f1f1;
            outline: none;
        }
        textarea{
            width:250px;
            height: 60px;
            padding: 6px;
            border: none;
            border: 2px black solid;
            background:#eeeeee;
            margin-top: 20px;
            text-align: right;
            border-radius: 7px;
        }
        textarea:focus{
            background: #f1f1f1;
            outline: none;
        }
        ::placeholder{
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
    
        .post {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            width: 300px;
        }
        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .post-content {
            margin-top: 10px;
        }
        .post-footer {
            margin-top: 10px;
            text-align: right;
        }
        header{
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #000;
            color: #fff;
            height: 50px;
            padding-left:30px;
            padding-right:30px;
        }
        h3{
            font-size: 22px;
        }
        a{
            border: 2px solid dodgerblue;
            padding: 7px;
            width:70px;
            border-radius: 10px;
            color: dodgerblue;
            text-decoration: none;
            transition: 0.40s;
        }
        a:hover{
            background: dodgerblue;
            color: #000;
        }
        span{
            color: dodgerblue;
        }
        select{
            width:260px;
            padding:10px;
            border: 2px #000 solid;
            border-radius: 5px;
        }
       .menu{
        box-shadow: 0px 0px 40px #333;
        padding:5px;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        width:30px;
        height:30px;
        position:fixed;
        right:0;
        top:50%;
       }
        .menu svg{
            fill: #222;
            width:30px;
            height:30px;
        }
        .post-footer a{
            border: none;
            color: darkred;
            transition: 0.50s;
        }
        .post-footer a:hover{
            border: none;
            color: darkred;
            text-decoration: underline;
            color: red;
            text-shadow: 0 0 10px red;
            background: none;
        }
         th, td{
            border: 1px black solid;
            padding: 10px;
        }
        .h2{
            padding: 20px;
            opacity: 0.70;
        }
    </style>
</head>
<body>
<div onclick="openPostSystem()" align="center" class="menu">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/></svg>
</div>
<header>
    <h3><?php echo htmlspecialchars($_SESSION["user"]); ?> <span>!</span></h3>
    <a href="logout.php" class="btn btn-warning"><center>Logout</center></a>
</header>

<div id="addPost" class="container">
    <h1 onclick="removePostSystem()">x</h1>
    <br><br>
    <!-- Post Form -->
    <form action="index.php" method="post" class="mt-4">
        <div class="form-group">
            <select name="blood_type" id="blood_type" class="form-control">
                <option align="center" value="">اختر نوع الدم</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" name="hospital" id="hospital" placeholder="أدخل المكان/المستشفي">
        </div>
        <div class="form-group">
            <input type="text" name="phone" id="phone" placeholder="أدخل رقم هاتفك">
        </div>
        <div class="form-group">
            <textarea type="text" name="caption" id="caption" placeholder="........"></textarea>.
        </div>

        <button type="submit" class="btn btn-primary mt-3">مشاركة</button>
    </form>
</div>
<br>

<!-- Display Posts -->
<div align="center" class="mt-5">
    <h2 class="h2">Recent Posts</h2><br>
    <?php
    if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="post">
                <div class="post-header">
                    <strong><?php echo htmlspecialchars($row["user_name"]); ?></strong>
                    <small><?php echo htmlspecialchars($row["created_at"]); ?></small>
                </div><br>
                <hr><br>
                <table class="post-content">
                    <tr>
                        <td>Blood Type: </td>
                        <td><?php echo htmlspecialchars($row["blood_type"]); ?></td>
                    </tr>
                    <tr>
                        <td>Place: </td>
                        <td><?php echo htmlspecialchars($row["hospital"]); ?></td>
                        
                    </tr>
                    <tr>
                        <td>Phone Num: </td>
                        <td><?php echo htmlspecialchars($row["phone"]); ?></td>
                    </tr>
                    <tr>
                        <td>Caption: </td>
                        <td><?php echo htmlspecialchars($row["caption"]); ?></td>
                    </tr>
               </table>
                <div class="post-footer">
                    <?php if ($_SESSION["user"] === $row['user_name']): ?>
                        <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete Post</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile;
    else: ?>
        <p>No posts found.</p>
    <?php endif; ?>
</div>

<script>
    let addPost = document.getElementById("addPost");

    function openPostSystem() {
        addPost.style.left = "50%";
    }

    function removePostSystem() {
        addPost.style.left = "-2000px";
    }
</script>
</body>
</html>
