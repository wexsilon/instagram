<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php", true, 301);
    exit;
}

$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);


$insertQueryComment = "INSERT INTO comments (author, pid, textt) VALUES (?, ?, ?)";
$stmt = $pdoObj->prepare($insertQueryComment);
$stmt->execute([$_SESSION["username"], $_GET["pid"], $_POST["text"]]);

$resp = array(
    "post_id" => $_GET["pid"],
    "photo" => $_SESSION["photo"],
    "owner" => $_SESSION["username"],
    "text" => $_POST["text"],
    "comment_id" => $pdoObj->lastInsertId()
);

$updateQueryPost = "UPDATE posts SET comment_count = comment_count + 1 WHERE id = ?";
$stmtUpdatePost = $pdoObj->prepare($updateQueryPost);
$stmtUpdatePost->execute([$_GET["pid"]]);

header("Content-Type: application/json");
echo json_encode($resp);
