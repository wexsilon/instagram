<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php", true, 301);
    exit;
}
$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);


$insertQueryLike = "INSERT INTO likes (pid, username) VALUES (?, ?)";
$stmtQueryLike = $pdoObj->prepare($insertQueryLike);
$stmtQueryLike->execute([$_GET['pid'], $_SESSION['username']]);

$updateQueryPost = "UPDATE posts SET like_count = like_count + 1 WHERE id = ?";
$stmtUpdatePost = $pdoObj->prepare($updateQueryPost);
$stmtUpdatePost->execute([$_GET["pid"]]);


$selectQueryPost = "SELECT like_count FROM posts WHERE id = ?";
$stmtQueryPost = $pdoObj->prepare($selectQueryPost);
$stmtQueryPost->execute([$_GET['pid']]);
$pc = $stmtQueryPost->fetch(PDO::FETCH_ASSOC);
$plc = intval($pc['like_count']);

$hsrc = "";
if ($plc > 1) {
    $hsrc .= "<p>$plc likes</p>";
}
elseif ($plc == 1) {
    $hsrc .= "<p>$plc like</p>";
}
else {
    $hsrc .= "<p></p>";
}
header("Content-Type: application/json");
echo json_encode(array(
    'html' => $hsrc
));

