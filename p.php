<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php", true, 301);
  
    exit;
}

$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);


$sqlQueryPost = 'SELECT id, username, photo, caption, like_count, comment_count, created_at FROM posts WHERE id = ?';
$stmtQueryPost = $pdoObj->prepare($sqlQueryPost);
$stmtQueryPost->execute([$_GET['pid']]);
$tpo = $stmtQueryPost->fetch(PDO::FETCH_ASSOC);
$pid = $tpo['id'];
$puser = $tpo['username'];
$pphoto = $tpo['photo'];
$pcaption = $tpo['caption'];
$plc = $tpo['like_count'];
$pcc = $tpo['comment_count'];
$pcreated = $tpo['created_at'];


$sqlQueryUser = 'SELECT photo FROM users WHERE username = ?';
$stmtQueryUser = $pdoObj->prepare($sqlQueryUser);
$stmtQueryUser->execute([$puser]);
$tpu = $stmtQueryUser->fetch(PDO::FETCH_ASSOC);
$puserphoto = $tpu['photo'];


$sqlQueryComment = 'SELECT id, author, textt, created_at FROM comments WHERE pid = ?';
$stmtQueryComment = $pdoObj->prepare($sqlQueryComment);
$stmtQueryComment->execute([$pid]);

$hcomments = "";

$fhcs = fopen("single_comment.html", "r");
$tmphcomment = fread($fhcs, filesize("single_comment.html"));
fclose($fhcs);


while ($comment = $stmtQueryComment->fetch(PDO::FETCH_ASSOC)){
    $cid = $comment['id'];
    $cauthor = $comment['author'];
    $ctext = $comment['textt'];
    $ccreated = $comment['created_at'];
    $sqlQueryUser = 'SELECT photo FROM users WHERE username = ?';
    $stmtQ = $pdoObj->prepare($sqlQueryUser);
    $stmtQ->execute([$cauthor]);
    $tca = $stmtQ->fetch(PDO::FETCH_ASSOC);
    $cauthorphoto = $tca['photo'];
    
//    $hcomment = str_replace('$cauthorphoto', $cauthorphoto, $tmphcomment);
//    $hcomment = str_replace('$cauthor', $cauthor, $hcomment);
//    $hcomment = str_replace('$ctext', $ctext, $hcomment);
//    $hcomment = str_replace('$ccreated', $ccreated, $hcomment);
    $hcomments .= str_replace(
        '$ccreated',
        $ccreated,
        str_replace(
            '$ctext',
            $ctext,
            str_replace(
                '$cauthor',
                $cauthor,
                str_replace(
                    '$cauthorphoto',
                    $cauthorphoto,
                    $tmphcomment
                )
            )
        )
    );
    

}

$fh = fopen("single_post.html", "r");
$hsrc = fread($fh, filesize("single_post.html"));
fclose($fh);
$hsrc = str_replace('$pphoto', $pphoto, $hsrc);
$hsrc = str_replace('$puserphoto', $puserphoto, $hsrc);
$hsrc = str_replace('$puser', $puser, $hsrc);
$hsrc = str_replace('$pid', $pid, $hsrc);
$hsrc = str_replace('$pcreated', $pcreated, $hsrc);
$hsrc = str_replace('$hcomments', $hcomments, $hsrc);


$selectQueryLike = "SELECT * FROM likes WHERE username = ? AND pid = ?";
$stmtQueryLike = $pdoObj->prepare($selectQueryLike);
$stmtQueryLike->execute([$_SESSION['username'], $pid]);

$is_liked = false;
if ($stmtQueryLike->rowCount() == 1) {
    $is_liked = true;
}

if ($is_liked) {
    $hsrc = str_replace('$lkbtns', '', $hsrc);
    $hsrc = str_replace('$ulkbtns', 'style="display: none;"', $hsrc);
} else {
    $hsrc = str_replace('$lkbtns', 'style="display: none;"', $hsrc);
    $hsrc = str_replace('$ulkbtns', '', $hsrc);    
}


$selectQuerySave = "SELECT * FROM saves WHERE username = ? AND pid = ?";
$stmtQuerSave = $pdoObj->prepare($selectQuerySave);
$stmtQuerSave->execute([$_SESSION['username'], $pid]);

$is_saved = false;
if ($stmtQuerSave->rowCount() == 1) {
    $is_saved = true;
}

if ($is_saved) {
    $hsrc = str_replace('$svbtn', '', $hsrc);
    $hsrc = str_replace('$unsvbtn', 'style="display: none;"', $hsrc);
} else {
    $hsrc = str_replace('$svbtn', 'style="display: none;"', $hsrc);
    $hsrc = str_replace('$unsvbtn', '', $hsrc);    
}


$hlikecount = '';
if ($plc > 1) {
    $hlikecount .= "<span>$plc likes</span>";
}
elseif ($plc  == 1) {
    $hlikecount .= "<span>$plc like</span>";
}
else {
    $hlikecount .= '<small style="font-weight: lighter !important;"> Be the first one to <b>like this</b> </small>';
}
$hsrc = str_replace('$hlikecount', $hlikecount, $hsrc);

header("Content-Type: application/json");
echo json_encode(array(
    'html' => $hsrc
));



