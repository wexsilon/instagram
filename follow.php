<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php", true, 301);
    exit;
}

$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);


$selectQueryFollow = "SELECT * FROM follows WHERE owner = ? AND reciever = ?";
$stmtQueryFollow = $pdoObj->prepare($selectQueryFollow);
$stmtQueryFollow->execute([$_SESSION['username'], $_POST['username']]);
$is_following = false;

if ($stmtQueryFollow->rowCount() == 1) {
    $is_following = false;
    $deleteQueryFollow = "DELETE FROM follows WHERE owner = ? AND reciever = ?";
    $stmtDeleteQueryFollow = $pdoObj->prepare($deleteQueryFollow);
    $stmtDeleteQueryFollow->execute([$_SESSION['username'], $_POST['username']]);
    
    $updateQueryOwner = "UPDATE users SET following_count = following_count - 1 WHERE username = ?";
    $stmtUpdateOwner = $pdoObj->prepare($updateQueryOwner);
    $stmtUpdateOwner->execute([$_SESSION['username']]);
    
    $updateQueryReciever = "UPDATE users SET follower_count = follower_count - 1 WHERE username = ?";
    $stmtUpdateReciever = $pdoObj->prepare($updateQueryReciever);
    $stmtUpdateReciever->execute([$_POST['username']]);
    
} else {
    $is_following = true;
    $insertQueryFollow = "INSERT INTO follows (owner, reciever) VALUES (?, ?)";
    $stmtQueryInsertFollow = $pdoObj->prepare($insertQueryFollow);
    $stmtQueryInsertFollow->execute([$_SESSION['username'], $_POST['username']]);
    
    $updateQueryOwner = "UPDATE users SET following_count = following_count + 1 WHERE username = ?";
    $stmtUpdateOwner = $pdoObj->prepare($updateQueryOwner);
    $stmtUpdateOwner->execute([$_SESSION['username']]);
    
    $updateQueryReciever = "UPDATE users SET follower_count = follower_count + 1 WHERE username = ?";
    $stmtUpdateReciever = $pdoObj->prepare($updateQueryReciever);
    $stmtUpdateReciever->execute([$_POST['username']]);
    
}

$hsrc = "";
if ($_SESSION['username'] == $_POST['username']) {
    $hsrc .= '<div class="btn btn-outline-secondary btn-sm"><span style="color:black;">Edit Profile</span></div>';
}
elseif (!$is_following) {
    $hsrc .= '<div class="btn btn-primary btn-sm follow-btn">Follow</div>';
}
else {
    $hsrc .= '<div class="btn btn-outline-secondary btn-sm follow-btn" style="color:black;"><img src="static/img/user-icon.png" style="width:80%;"></div>';
}

header("Content-Type: application/json");
echo json_encode(array(
    'html' => $hsrc
));