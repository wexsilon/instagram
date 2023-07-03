<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php", true, 301);
    exit;
}
$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);

require_once "models.php";

function is_liked($pid, $un) {
    global $pdoObj;
    $sql = "SELECT * FROM likes WHERE username = ? AND pid = ?";
    $stmt = $pdoObj->prepare($sql);
    $stmt->execute([$un, $pid]);
    $rows = $stmt->rowCount();
    return $rows == 1;
}

function is_saved($pid, $un) {
    global $pdoObj;
    $sql = "SELECT * FROM saves WHERE username = ? AND pid = ?";
    $stmt = $pdoObj->prepare($sql);
    $stmt->execute([$un, $pid]);
    $rows = $stmt->rowCount();
    return $rows == 1;

}




$page_obj = array();

$arr_usernames = array($_SESSION['username']);

$selectFollowings = "SELECT reciever FROM follows WHERE owner = ?";
$stmtSelectFollowings = $pdoObj->prepare($selectFollowings);
$stmtSelectFollowings->execute([$_SESSION['username']]);

while ($fs = $stmtSelectFollowings->fetch(PDO::FETCH_ASSOC)){
    $recvr = $fs['reciever'];
    array_push($arr_usernames, $recvr);
}




foreach($arr_usernames as $reciever) {
    $selectFollowingPosts = "SELECT id, photo, like_count, comment_count, caption, created_at FROM posts WHERE username = ?";
    $stmtSelectFollowingPost = $pdoObj->prepare($selectFollowingPosts);
    $stmtSelectFollowingPost->execute([$reciever]);
    if ($stmtSelectFollowingPost->rowCount() > 0) {
        
        $selectPhotoUsers = "SELECT photo FROM users WHERE username = ?";
        $stmtSelectPhotoUser = $pdoObj->prepare($selectPhotoUsers);
        $stmtSelectPhotoUser->execute([$reciever]);
        $tpt = $stmtSelectPhotoUser->fetch(PDO::FETCH_ASSOC);
        $uphoto = $tpt['photo'];
        $uf = new User($reciever, $uphoto);
        while ($fp = $stmtSelectFollowingPost->fetch(PDO::FETCH_ASSOC)) {
            $pid = $fp['id'];
            $pphoto = $fp['photo'];
            $plc = intval($fp['like_count']);
            $pcc = intval($fp['comment_count']);
            $pcaption = $fp['caption'];
            $pcreated = $fp['created_at'];
            
            $p = new Post();
            $p->user = $uf;
            $p->id = $pid;
            $p->photo = $pphoto;
            $p->like_count = $plc;
            $p->comment_count = $pcc;
            $p->caption = $pcaption;
            $p->created_at = $pcreated;
            $p->is_liked = is_liked($pid, $_SESSION['username']);
            $p->is_saved = is_saved($pid, $_SESSION['username']);
            array_push($page_obj, $p);
            
            $selectPostComments = "SELECT author, textt FROM comments WHERE pid = ? ORDER BY created_at LIMIT 2";
            $stmtSelectPostComment = $pdoObj->prepare($selectPostComments);
            $stmtSelectPostComment->execute([$pid]);
            while ($comment = $stmtSelectPostComment->fetch(PDO::FETCH_ASSOC)){
                $cauthor = $comment['author'];
                $ctext = $comment['textt'];
                $p->addComment($cauthor, $ctext);
            }
        }
        
    }
    
}

usort($page_obj, function ($a, $b) {
    if ($a->created_at < $b->created_at) {
        return 1;
    } elseif ($a->created_at == $b->created_at) {
        return 0;
    } else {
        return -1;
    }
});


$selectFullname = "SELECT fullname FROM users WHERE username = ?";
$stmtSelectFullname = $pdoObj->prepare($selectFullname);
$stmtSelectFullname->execute([$_SESSION['username']]);
$tfn = $stmtSelectFullname->fetch(PDO::FETCH_ASSOC);
$fullname = $tfn['fullname'];



?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://kit.fontawesome.com/68a84641e6.js" crossorigin="anonymous"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="static/css/base.css">
        <script src="static/js/jquery.waypoints.min.js"></script>
        <script src="static/js/infinite.min.js"></script>
        <link rel="icon" href="static/img/favicon.ico">
        <title> Instagram </title>

    </head>

    <body>

        <div class="loader">
            <svg width="50" height="50" viewBox="0 0 50 50" style="position:absolute;top:50%;left:50%;margin:-25px 0 0 -25px;fill:#c7c7c7"><path d="M25 1c-6.52 0-7.34.03-9.9.14-2.55.12-4.3.53-5.82 1.12a11.76 11.76 0 0 0-4.25 2.77 11.76 11.76 0 0 0-2.77 4.25c-.6 1.52-1 3.27-1.12 5.82C1.03 17.66 1 18.48 1 25c0 6.5.03 7.33.14 9.88.12 2.56.53 4.3 1.12 5.83a11.76 11.76 0 0 0 2.77 4.25 11.76 11.76 0 0 0 4.25 2.77c1.52.59 3.27 1 5.82 1.11 2.56.12 3.38.14 9.9.14 6.5 0 7.33-.02 9.88-.14 2.56-.12 4.3-.52 5.83-1.11a11.76 11.76 0 0 0 4.25-2.77 11.76 11.76 0 0 0 2.77-4.25c.59-1.53 1-3.27 1.11-5.83.12-2.55.14-3.37.14-9.89 0-6.51-.02-7.33-.14-9.89-.12-2.55-.52-4.3-1.11-5.82a11.76 11.76 0 0 0-2.77-4.25 11.76 11.76 0 0 0-4.25-2.77c-1.53-.6-3.27-1-5.83-1.12A170.2 170.2 0 0 0 25 1zm0 4.32c6.4 0 7.16.03 9.69.14 2.34.11 3.6.5 4.45.83 1.12.43 1.92.95 2.76 1.8a7.43 7.43 0 0 1 1.8 2.75c.32.85.72 2.12.82 4.46.12 2.53.14 3.29.14 9.7 0 6.4-.02 7.16-.14 9.69-.1 2.34-.5 3.6-.82 4.45a7.43 7.43 0 0 1-1.8 2.76 7.43 7.43 0 0 1-2.76 1.8c-.84.32-2.11.72-4.45.82-2.53.12-3.3.14-9.7.14-6.4 0-7.16-.02-9.7-.14-2.33-.1-3.6-.5-4.45-.82a7.43 7.43 0 0 1-2.76-1.8 7.43 7.43 0 0 1-1.8-2.76c-.32-.84-.71-2.11-.82-4.45a166.5 166.5 0 0 1-.14-9.7c0-6.4.03-7.16.14-9.7.11-2.33.5-3.6.83-4.45a7.43 7.43 0 0 1 1.8-2.76 7.43 7.43 0 0 1 2.75-1.8c.85-.32 2.12-.71 4.46-.82 2.53-.11 3.29-.14 9.7-.14zm0 7.35a12.32 12.32 0 1 0 0 24.64 12.32 12.32 0 0 0 0-24.64zM25 33a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm15.68-20.8a2.88 2.88 0 1 0-5.76 0 2.88 2.88 0 0 0 5.76 0z"></path></svg>            
        </div>
        <div class="modal fade single-post-view" id='single-post-view' tabindex="-1" role="dialog" aria-labelledby="share-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width:950px; height:75vh;">
                <div class="modal-content">
                    <div class="modal-body single-post-content row justify-content-center" style="padding-left:0; height: 85vh;">
                    </div>
                </div>
            </div>
        </div>
        <nav class="nav bg-white justify-content-center align-items-center list-inline" >

            <ul class='col-md-9 col-12' type='none'>
                <li class="col-3 col-md-4"><a href="/"><img alt="Instagram" class="logo" src="static/img/logo.png"></a></li>
                <li class='search col-4 d-none d-md-block justify-content-center align-items-center'>
                    <form class="search-form position-relative col-8 border p-0" onsubmit="event.preventDefault()" autocomplete="off" style="border-radius: 4px;">
                        <input class='col-12 m-0 p-0 font-weight-light' id="user-input" type="text" placeholder="Search" style="font-family:Arial, FontAwesome" aria-label="Search" onfocus="clearField(this)">
                        <svg id='load-icon' aria-label="Loading..." class="fa-spin" viewBox="0 0 100 100">
                            <rect fill="#555555" height="6" opacity="0" rx="3" ry="3" transform="rotate(-90 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.08333333333333333" rx="3" ry="3" transform="rotate(-60 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.16666666666666666" rx="3" ry="3" transform="rotate(-30 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.25" rx="3" ry="3" transform="rotate(0 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.3333333333333333" rx="3" ry="3" transform="rotate(30 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.4166666666666667" rx="3" ry="3" transform="rotate(60 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.5" rx="3" ry="3" transform="rotate(90 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.5833333333333334" rx="3" ry="3" transform="rotate(120 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.6666666666666666" rx="3" ry="3" transform="rotate(150 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.75" rx="3" ry="3" transform="rotate(180 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.8333333333333334" rx="3" ry="3" transform="rotate(210 50 50)" width="25" x="72" y="47"></rect>
                            <rect fill="#555555" height="6" opacity="0.9166666666666666" rx="3" ry="3" transform="rotate(240 50 50)" width="25" x="72" y="47"></rect>
                        </svg>
                    </form>
                    <div class="triangle-up"></div>
                    <div id='search-results'>
                        <div id='no-result'>No results found.</div>
                    </div>
                </li>

                <li class="nav-links col-md-4 col-9 d-flex justify-content-end">

                    <div id='nav-home' class='nav-link-item col-2' id='home'>
                        <a href="post_list.php">
                            <svg aria-label="Home" class="_8-yf5 " fill="#262626" height="22" viewBox="0 0 48 48" width="22"><path d="M45.3 48H30c-.8 0-1.5-.7-1.5-1.5V34.2c0-2.6-2-4.6-4.6-4.6s-4.6 2-4.6 4.6v12.3c0 .8-.7 1.5-1.5 1.5H2.5c-.8 0-1.5-.7-1.5-1.5V23c0-.4.2-.8.4-1.1L22.9.4c.6-.6 1.5-.6 2.1 0l21.5 21.5c.4.4.6 1.1.3 1.6 0 .1-.1.1-.1.2v22.8c.1.8-.6 1.5-1.4 1.5zm-13.8-3h12.3V23.4L24 3.6l-20 20V45h12.3V34.2c0-4.3 3.3-7.6 7.6-7.6s7.6 3.3 7.6 7.6V45z"></path>
                            </svg>
                        </a>
                    </div>
                    <div id='nav-explore' class='nav-link-item col-2' id='explore'>
                        <a href="explore.php">
                            <svg aria-label="Find People" class="_8-yf5 " fill="#262626" height="22" viewBox="0 0 48 48" width="22"><path clip-rule="evenodd" d="M24 0C10.8 0 0 10.8 0 24s10.8 24 24 24 24-10.8 24-24S37.2 0 24 0zm0 45C12.4 45 3 35.6 3 24S12.4 3 24 3s21 9.4 21 21-9.4 21-21 21zm10.2-33.2l-14.8 7c-.3.1-.6.4-.7.7l-7 14.8c-.3.6-.2 1.3.3 1.7.3.3.7.4 1.1.4.2 0 .4 0 .6-.1l14.8-7c.3-.1.6-.4.7-.7l7-14.8c.3-.6.2-1.3-.3-1.7-.4-.5-1.1-.6-1.7-.3zm-7.4 15l-5.5-5.5 10.5-5-5 10.5z" fill-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                    <div id='nav-profile' class="dropdown nav-link-item col-2">
                        <a  href="profile.php" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            <img class='rounded-circle' src="<?php echo $_SESSION['photo'] ?>" style='width: 25px; height: 25px;'>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="profile.php">
                                <svg aria-label="Profile" class="_8-yf5 " fill="#262626" height="16" viewBox="0 0 32 32" width="16"><path d="M16 0C7.2 0 0 7.1 0 16c0 4.8 2.1 9.1 5.5 12l.3.3C8.5 30.6 12.1 32 16 32s7.5-1.4 10.2-3.7l.3-.3c3.4-3 5.5-7.2 5.5-12 0-8.9-7.2-16-16-16zm0 29c-2.8 0-5.3-.9-7.5-2.4.5-.9.9-1.3 1.4-1.8.7-.5 1.5-.8 2.4-.8h7.2c.9 0 1.7.3 2.4.8.5.4.9.8 1.4 1.8-2 1.5-4.5 2.4-7.3 2.4zm9.7-4.4c-.5-.9-1.1-1.5-1.9-2.1-1.2-.9-2.7-1.4-4.2-1.4h-7.2c-1.5 0-3 .5-4.2 1.4-.8.6-1.4 1.2-1.9 2.1C4.2 22.3 3 19.3 3 16 3 8.8 8.8 3 16 3s13 5.8 13 13c0 3.3-1.2 6.3-3.3 8.6zM16 5.7c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 11c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"></path></svg>
                                <span>Profile</span>
                            </a>
                            <a class="dropdown-item" href="saved.php">
                                <svg aria-label="Saved" class="_8-yf5 " fill="#262626" height="16" viewBox="0 0 32 32" width="16"><path d="M28.7 32c-.4 0-.8-.2-1.1-.4L16 19.9 4.4 31.6c-.4.4-1.1.6-1.6.3-.6-.2-.9-.8-.9-1.4v-29C1.8.7 2.5 0 3.3 0h25.4c.8 0 1.5.7 1.5 1.5v29c0 .6-.4 1.2-.9 1.4-.2.1-.4.1-.6.1zM4.8 3v23.9l9.4-9.4c.9-.9 2.6-.9 3.5 0l9.4 9.4V3H4.8z"></path></svg>                      
                                <span>Saved</span>
                            </a>
                            <a class="dropdown-item" href="post_create.php">
                                <svg aria-label="New Post" class="_8-yf5 " fill="#262626" height="18" viewBox="0 0 48 48" width="18"><path d="M31.8 48H16.2c-6.6 0-9.6-1.6-12.1-4C1.6 41.4 0 38.4 0 31.8V16.2C0 9.6 1.6 6.6 4 4.1 6.6 1.6 9.6 0 16.2 0h15.6c6.6 0 9.6 1.6 12.1 4C46.4 6.6 48 9.6 48 16.2v15.6c0 6.6-1.6 9.6-4 12.1-2.6 2.5-5.6 4.1-12.2 4.1zM16.2 3C10 3 7.8 4.6 6.1 6.2 4.6 7.8 3 10 3 16.2v15.6c0 6.2 1.6 8.4 3.2 10.1 1.6 1.6 3.8 3.1 10 3.1h15.6c6.2 0 8.4-1.6 10.1-3.2 1.6-1.6 3.1-3.8 3.1-10V16.2c0-6.2-1.6-8.4-3.2-10.1C40.2 4.6 38 3 31.8 3H16.2z"></path><path d="M36.3 25.5H11.7c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5h24.6c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5z"></path><path d="M24 37.8c-.8 0-1.5-.7-1.5-1.5V11.7c0-.8.7-1.5 1.5-1.5s1.5.7 1.5 1.5v24.6c0 .8-.7 1.5-1.5 1.5z"></path></svg>                      
                                <span>New Post</span>
                            </a>
                            <a class="dropdown-item" href="edit.php">
                                <svg aria-label="Settings" class="_8-yf5 " fill="#262626" height="16" viewBox="0 0 32 32" width="16"><path d="M31.2 13.4l-1.4-.7c-.1 0-.2-.1-.2-.2v-.2c-.3-1.1-.7-2.1-1.3-3.1v-.1l-.2-.1v-.3l.5-1.5c.2-.5 0-1.1-.4-1.5l-1.9-1.9c-.4-.4-1-.5-1.5-.4l-1.5.5H23l-.1-.1h-.1c-1-.5-2-1-3.1-1.3h-.2c-.1 0-.1-.1-.2-.2L18.6.9c-.2-.5-.7-.9-1.2-.9h-2.7c-.5 0-1 .3-1.3.8l-.7 1.4c0 .1-.1.2-.2.2h-.2c-1.1.3-2.1.7-3.1 1.3h-.1l-.1.2h-.3l-1.5-.5c-.5-.2-1.1 0-1.5.4L3.8 5.7c-.4.4-.5 1-.4 1.5l.5 1.5v.5c-.5 1-1 2-1.3 3.1v.2c0 .1-.1.1-.2.2l-1.4.7c-.6.2-1 .7-1 1.2v2.7c0 .5.3 1 .8 1.3l1.4.7c.1 0 .2.1.2.2v.2c.3 1.1.7 2.1 1.3 3.1v.1l.2.1v.3l-.5 1.5c-.2.5 0 1.1.4 1.5l1.9 1.9c.3.3.6.4 1 .4.2 0 .3 0 .5-.1l1.5-.5H9l.1.1h.1c1 .5 2 1 3.1 1.3h.2c.1 0 .1.1.2.2l.7 1.4c.2.5.7.8 1.3.8h2.7c.5 0 1-.3 1.3-.8l.7-1.4c0-.1.1-.2.2-.2h.2c1.1-.3 2.1-.7 3.1-1.3h.1l.1-.1h.3l1.5.5c.1 0 .3.1.5.1.4 0 .7-.1 1-.4l1.9-1.9c.4-.4.5-1 .4-1.5l-.5-1.5V23l.1-.1v-.1c.5-1 1-2 1.3-3.1v-.2c0-.1.1-.1.2-.2l1.4-.7c.5-.2.8-.7.8-1.3v-2.7c0-.5-.4-1-.8-1.2zM16 27.1c-6.1 0-11.1-5-11.1-11.1S9.9 4.9 16 4.9s11.1 5 11.1 11.1-5 11.1-11.1 11.1z"></path></svg>                      
                                <span>Settings</span>
                            </a>
                            <div class="dropdown-divider" style="margin-bottom:0;"></div>
                            <a class="dropdown-item" href="logout.php">Logout</a>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
        <main role="main" class="container-fluid">
            <div class='row justify-content-center'>         
                <script src="static/js/base.js"></script>
                <div class='row col-12 col-lg-9 justify-content-start' style="padding: 0; margin: 0;">
                    <div class='main-content col-xs-12 col-md-11 col-lg-8 '>
                        <div class="infinite-container">
                        <?php
                        foreach ($page_obj as $post) {
                        ?>
                            <div class="infinite-item post card" id='post-<?php echo $post->id; ?>'>
                                <div class="card-header">
                                    <img class="rounded-circle post-owner-img" src="<?php echo $post->user->photo; ?>" style="width:32px;">
                                    <a class="owner-username mr-2 text-dark" href="profile.php?username=<?php echo $post->user->username ?>"> <?php echo $post->user->username ?></a>
                                </div>
                                <div class='post-img-container'>
                                    <img class="post-img" id='post-img-<?php echo $post->id; ?>' src="<?php echo $post->photo; ?>">
                                    <svg aria-label="Unlike" class="heart" fill="#fff" height="25" viewBox="0 0 48 48" width="26" >
                                        <path d="M34.6 3.1c-4.5 0-7.9 1.8-10.6 5.6-2.7-3.7-6.1-5.5-10.6-5.5C6 3.1 0 9.6 0 17.6c0 7.3 5.4 12 10.6 16.5.6.5 1.3 1.1 1.9 1.7l2.3 2c4.4 3.9 6.6 5.9 7.6 6.5.5.3 1.1.5 1.6.5s1.1-.2 1.6-.5c1-.6 2.8-2.2 7.8-6.8l2-1.8c.7-.6 1.3-1.2 2-1.7C42.7 29.6 48 25 48 17.6c0-8-6-14.5-13.4-14.5z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="card-body">
                                    <div class='buttons col-12 d-inline-flex'>
                                        <div class='like_btn'>            
                                            <a onclick="likePost('post_unlike.php?pid=<?php echo $post->id; ?>', <?php echo $post->id; ?> );return false;"
                                                <?php if (!$post->is_liked) { echo 'style="display: none;"'; } ?>
                                                class="like-btn like-btn-<?php echo $post->id; ?>" id="like-btn-<?php echo $post->id; ?>">
                                                <svg aria-label="Unlike" class="_8-yf5 " fill="#ed4956" height="25" viewBox="0 0 48 48" width="26" >
                                                    <path d="M34.6 3.1c-4.5 0-7.9 1.8-10.6 5.6-2.7-3.7-6.1-5.5-10.6-5.5C6 3.1 0 9.6 0 17.6c0 7.3 5.4 12 10.6 16.5.6.5 1.3 1.1 1.9 1.7l2.3 2c4.4 3.9 6.6 5.9 7.6 6.5.5.3 1.1.5 1.6.5s1.1-.2 1.6-.5c1-.6 2.8-2.2 7.8-6.8l2-1.8c.7-.6 1.3-1.2 2-1.7C42.7 29.6 48 25 48 17.6c0-8-6-14.5-13.4-14.5z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a onclick="likePost('post_like.php?pid=<?php echo $post->id; ?>', <?php echo $post->id; ?> );return false;"
                                                <?php if ($post->is_liked) { echo 'style="display: none;"'; } ?>
                                                class="unlike-btn unlike-btn-<?php echo $post->id; ?>" id="unlike-btn-<?php echo $post->id; ?>">
                                                <svg aria-label="Like" class="_8-yf5 " fill="#262626" height="25" viewBox="0 0 48 48" width="26" >
                                                    <path d="M34.6 6.1c5.7 0 10.4 5.2 10.4 11.5 0 6.8-5.9 11-11.5 16S25 41.3 24 41.9c-1.1-.7-4.7-4-9.5-8.3-5.7-5-11.5-9.2-11.5-16C3 11.3 7.7 6.1 13.4 6.1c4.2 0 6.5 2 8.1 4.3 1.9 2.6 2.2 3.9 2.5 3.9.3 0 .6-1.3 2.5-3.9 1.6-2.3 3.9-4.3 8.1-4.3m0-3c-4.5 0-7.9 1.8-10.6 5.6-2.7-3.7-6.1-5.5-10.6-5.5C6 3.1 0 9.6 0 17.6c0 7.3 5.4 12 10.6 16.5.6.5 1.3 1.1 1.9 1.7l2.3 2c4.4 3.9 6.6 5.9 7.6 6.5.5.3 1.1.5 1.6.5.6 0 1.1-.2 1.6-.5 1-.6 2.8-2.2 7.8-6.8l2-1.8c.7-.6 1.3-1.2 2-1.7C42.7 29.6 48 25 48 17.6c0-8-6-14.5-13.4-14.5z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>          

                                        <div class='all-comments' id='all-comments-<?php echo $post->id; ?>' data-toggle="modal" data-target="#single-post-view"></divw>
                                            <svg aria-label="Comment" class="_8-yf5 " fill="#262626" height="24" viewBox="0 0 48 48" width="24">
                                                <path clip-rule="evenodd" d="M47.5 46.1l-2.8-11c1.8-3.3 2.8-7.1 2.8-11.1C47.5 11 37 .5 24 .5S.5 11 .5 24 11 47.5 24 47.5c4 0 7.8-1 11.1-2.8l11 2.8c.8.2 1.6-.6 1.4-1.4zm-3-22.1c0 4-1 7-2.6 10-.2.4-.3.9-.2 1.4l2.1 8.4-8.3-2.1c-.5-.1-1-.1-1.4.2-1.8 1-5.2 2.6-10 2.6-11.4 0-20.6-9.2-20.6-20.5S12.7 3.5 24 3.5 44.5 12.7 44.5 24z" fill-rule="evenodd">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class='save_btn'>
                                            <a onclick="savePost('post_unsave.php?pid=<?php echo $post->id; ?>', <?php echo $post->id; ?> );return false;"
                                                <?php if (!$post->is_saved) { echo 'style="display: none;"'; } ?>
                                                class="save-btn save-btn-<?php echo $post->id; ?>" id="save-btn-<?php echo $post->id; ?>">
                                                <svg aria-label="Save" class="_8-yf5 " fill="#262626" height="24" viewBox="0 0 48 48" width="24">
                                                    <path d="M43.5 48c-.4 0-.8-.2-1.1-.4L24 28.9 5.6 47.6c-.4.4-1.1.6-1.6.3-.6-.2-1-.8-1-1.4v-45C3 .7 3.7 0 4.5 0h39c.8 0 1.5.7 1.5 1.5v45c0 .6-.4 1.2-.9 1.4-.2.1-.4.1-.6.1z">
                                                    </path>
                                                </svg>  
                                            </a>
                                            <a onclick="savePost('post_save.php?pid=<?php echo $post->id; ?>', <?php echo $post->id; ?> );return false;"
                                                <?php if ($post->is_saved) {echo 'style="display: none;"';} ?>
                                                class="unsave-btn unsave-btn-<?php echo $post->id; ?>" id="unsave-btn-<?php echo $post->id; ?>">
                                                <svg aria-label="Unsave" class="_8-yf5 " fill="#262626" height="24" viewBox="0 0 48 48" width="24" id='save_btn'>
                                                    <path d="M43.5 48c-.4 0-.8-.2-1.1-.4L24 29 5.6 47.6c-.4.4-1.1.6-1.6.3-.6-.2-1-.8-1-1.4v-45C3 .7 3.7 0 4.5 0h39c.8 0 1.5.7 1.5 1.5v45c0 .6-.4 1.2-.9 1.4-.2.1-.4.1-.6.1zM24 26c.8 0 1.6.3 2.2.9l15.8 16V3H6v39.9l15.8-16c.6-.6 1.4-.9 2.2-.9z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    <div class='post-like post-like-<?php echo $post->id; ?>' id='post-like-<?php echo $post->id; ?>'>
                                        <p>
                                            <?php
                                            
                                            if($post->like_count > 1) {
                                                echo $post->like_count;
                                                echo ' likes';
                                            } elseif ($post->like_count == 1) {
                                                echo $post->like_count;
                                                echo ' like';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <?php 
                                    if (!empty($post->caption)) {
                                    ?>
                                    <span class='owner-username mt-3'><?php echo $post->user->username; ?></span> <?php echo $post->caption; ?>
                                    <?php
                                    }
                                    ?>
                                    
                                    <?php
                                    if ($post->comment_count > 2) {
                                    ?>
                                    <div class='all-comments text-muted' id='all-comments-<?php echo $post->id; ?>'> View all <?php echo $post->comment_count ?> comments </div>
                                    <?php
                                    }
                                    ?>
                                    <div class='comment-section comment-section-<?php echo $post->id; ?>' id='comment-section-<?php echo $post->id; ?>'>
                                        <?php
                                        foreach($post->comments as $comment) {
                                        ?>
                                        <p> <span class='owner-username'><a href="profile.php?username=<?php echo $comment->author?>"><?php echo $comment->author?></a></span>  <?php echo $comment->text?></p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <small class="text-muted" style="font-size: 65%;"><?php echo $post->created_at; ?></small>
                                </div>
                                <div class="row card-footer justify-content-start">
                                    <form method="post" class='commentForm col-12 p-0' id='commentForm-<?php echo $post->id; ?>' name='commentForm' autocomplete="off" spellcheck="false">
                                        <span class='emoji mr-1'>
                                            <img class="rounded-circle post-owner-img" src="<?php echo $_SESSION['photo']; ?>" style="width:32px;">
                                            <svg aria-label="Emoji" fill="#262626" height="24" viewBox="0 0 48 48" width="24"><path d="M24 48C10.8 48 0 37.2 0 24S10.8 0 24 0s24 10.8 24 24-10.8 24-24 24zm0-45C12.4 3 3 12.4 3 24s9.4 21 21 21 21-9.4 21-21S35.6 3 24 3z"></path><path d="M34.9 24c0-1.4-1.1-2.5-2.5-2.5s-2.5 1.1-2.5 2.5 1.1 2.5 2.5 2.5 2.5-1.1 2.5-2.5zm-21.8 0c0-1.4 1.1-2.5 2.5-2.5s2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5-2.5-1.1-2.5-2.5zM24 37.3c-5.2 0-8-3.5-8.2-3.7-.5-.6-.4-1.6.2-2.1.6-.5 1.6-.4 2.1.2.1.1 2.1 2.5 5.8 2.5 3.7 0 5.8-2.5 5.8-2.5.5-.6 1.5-.7 2.1-.2.6.5.7 1.5.2 2.1 0 .2-2.8 3.7-8 3.7z"></path>
                                            </svg>
                                        </span>
                                        <input type="text" name="text" placeholder="Add a comment..." maxlength="250" minlength="1" id="id_text">
                                        <input type='submit' class='comment-submit col-5 col-md-6 justify-content-end text-right' value="Post"> 
                                        <input type='hidden' name='url' id='url' value='post_comment_create.php?pid=<?php echo $post->id; ?>'>
                                    </form>       
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        </div>
                    </div>

                    <div class="side-content col-md-4 d-none d-lg-block">
                        <div class="content-section">
                            <div class='media'>
                                <div class='media-left' style="margin-right:2%;">
                                    <img class="rounded-circle post-owner-img" src="<?php echo $_SESSION['photo']; ?>" style="width:56px;">
                                </div>
                                <div class='media-body' style="margin-top:2%;">
                                    <div class='media-heading'>
                                        <a class="owner-username mr-2 text-dark" href="profile.php"> <?php echo $_SESSION['username'] ?></a>
                                    </div>
                                    <div class='text-muted'> <?php echo $fullname; ?></div>
                                </div>
                            </div>
    
                            <small style="color:#cacaca; font-size:70%; cursor: pointer;">
                                About &middot; Help &middot; Press &middot; API &middot; Jobs &middot; Privacy &middot; Terms &middot; Locations <br>
                                Top Accounts &middot; Hashtags &middot; Language <br><br>© 2021 INSTAGRAM FROM FACEBOOK
                            </small>
                        </div>
                    </div> 
                </div>
                <footer class='col-12 mb-2 mt-5 justify-content-center' style="font-size: 0.75rem; color:#8e8e8e;">
                    <p class='text-center'style='cursor:pointer;'> About  &emsp;Blog  &emsp;Jobs &emsp; Help &emsp; API  &emsp;Privacy &emsp; Terms &emsp; Top Accounts &emsp; Hashtags &emsp;Locations</p>
                    <p class='text-center'style='cursor:pointer;'> English  © 2021 Instagram from Facebook</p>
                </footer>
                <script src="static/js/jquery.waypoints.min.js"></script>
                <script src="static/js/infinite.min.js"></script>
                <script>
                    var infinite = new Waypoint.Infinite({
                        element: $('.infinite-container')[0],
                        offset: 'bottom-in-view',
                        onBeforePageLoad: function () {
                            $('.loading-posts').show();
                        },
                        onAfterPageLoad: function () {
                            $('.loading-posts').hide();
                        }
                    });
                    $(function() {
                        $('#nav-home>a').html('<svg aria-label="Home" class="_8-yf5 " fill="#262626" height="22" viewBox="0 0 48 48" width="22"><path d="M45.5 48H30.1c-.8 0-1.5-.7-1.5-1.5V34.2c0-2.6-2.1-4.6-4.6-4.6s-4.6 2.1-4.6 4.6v12.3c0 .8-.7 1.5-1.5 1.5H2.5c-.8 0-1.5-.7-1.5-1.5V23c0-.4.2-.8.4-1.1L22.9.4c.6-.6 1.6-.6 2.1 0l21.5 21.5c.3.3.4.7.4 1.1v23.5c.1.8-.6 1.5-1.4 1.5z"></path></svg>')
                    });
                </script>
            </div>
        </main>

        <script>
            const loader = document.querySelector(".loader");
            window.onload = function () {
                setTimeout(function () {
                    loader.style.opacity = "0";
                    loader.style.display = "none";
                }, 250);
            };
        </script>

    </body>
</html>