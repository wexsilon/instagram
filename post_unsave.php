<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php", true, 301);
    exit;
}
$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);

$deletetQuerySave = "DELETE FROM saves WHERE pid = ? AND username = ?";
$stmtQuerySave = $pdoObj->prepare($deletetQuerySave);
$stmtQuerySave->execute([$_GET['pid'], $_SESSION['username']]);
echo '';