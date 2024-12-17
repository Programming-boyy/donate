<?php
session_start();
require_once "database.php";

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Retrieve the post details to ensure the user is the owner
    $sql = "SELECT user_name FROM posts WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        if ($_SESSION["user"] === $row['user_name']) {
            // Delete the post if the user is the owner
            $sql = "DELETE FROM posts WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $postId);
            mysqli_stmt_execute($stmt);

            header("Location: index.php");
            exit();
        }
    }

    // Redirect to index.php if the user is not the owner
    header("Location: index.php");
    exit();
}
?>