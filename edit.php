<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php", true, 301);
    exit;
}


$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);

$sql = "SELECT fullname, username, bio, photo, website, phone, email FROM users WHERE id = ?";
$stmt = $pdoObj->prepare($sql);
$stmt->execute([$_SESSION["id"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$fullname = $user['fullname'];
$username = $user['username'];
$bio = $user['bio'];
$photo = $user['photo'];
$website = $user['website'];
$phone = $user['phone'];
$email = $user['email'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $updateQuery = "UPDATE users SET ";
    $formatParam = "";
    $arrayParam = array();
    if(!empty(trim($_POST["username"]))) {
        $updateQuery .= "username = ?, ";
        $_SESSION["username"] = $_POST["username"];
        $formatParam .= "s";
        array_push($arrayParam, $_POST["username"]);
    }
    
    if(!empty(trim($_POST["fullname"]))) {
        $updateQuery .= "fullname = ?, ";
        $formatParam .= "s";
        array_push($arrayParam, $_POST["fullname"]);
    }
    
    if(!empty(trim($_POST["email"]))) {
        $updateQuery .= "email = ?, ";
        $formatParam .= "s";
        array_push($arrayParam, $_POST["email"]);
    }
    
    if(!empty(trim($_POST["website"]))) {
        $updateQuery .= "website = ?, ";
        $formatParam .= "s";
        array_push($arrayParam, $_POST["website"]);
    }
    
    if(!empty(trim($_POST["bio"]))) {
        $updateQuery .= "bio = ?, ";
        $formatParam .= "s";
        array_push($arrayParam, $_POST["bio"]);        
    }
    
    if(!empty(trim($_POST["phone"]))) {
        $updateQuery .= "phone = ?, ";
        $formatParam .= "s";
        array_push($arrayParam, $_POST["phone"]);   
        
    }
    
    if (isset($_FILES['photo'])) {
        if ($_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            if ($_FILES['photo']['size'] > 0 && $_FILES['photo']['size'] < 500000) {
                if (getimagesize($_FILES["photo"]["tmp_name"]) !== false) {
                    $photoBaseName = basename($_FILES["photo"]["name"]);
                    $imageFileType = strtolower(pathinfo($photoBaseName, PATHINFO_EXTENSION));
                    if (in_array($imageFileType, array('jpg', 'png', 'jpeg', 'gif'))) {
                        $updateQuery .= "photo = ?, ";
                        $formatParam .= "s";
                        $outf = "static/media/";
                        $outf .= md5(date('Y-m-d h:i:s').$photoBaseName).'.';
                        $outf .= $imageFileType;
                        move_uploaded_file($_FILES['photo']['tmp_name'], $outf);
                        array_push($arrayParam, $outf);
                        $_SESSION["photo"] = $outf;
                    }
                }
            }
        }
    }
    $updateQuery = substr(trim($updateQuery), 0, -1);
    $updateQuery .= " WHERE id = ?;";
    $formatParam .= "i";
    array_push($arrayParam, $_SESSION["id"]);
    
    $stmt = $pdoObj->prepare($updateQuery);
    $stmt->execute($arrayParam);
    $stmt->closeCursor();
    
    header("Location: profile.php", true, 301);
}
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
                        <input class='col-12 m-0 p-0 font-weight-light' id="user-input" type="text" placeholder="&#xF002; Search" style="font-family:Arial, FontAwesome" aria-label="Search" onfocus="clearField(this)">
                        <svg id='load-icon' aria-label="Loading..." class="fa-spin" viewBox="0 0 100 100"><rect fill="#555555" height="6" opacity="0" rx="3" ry="3" transform="rotate(-90 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.08333333333333333" rx="3" ry="3" transform="rotate(-60 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.16666666666666666" rx="3" ry="3" transform="rotate(-30 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.25" rx="3" ry="3" transform="rotate(0 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.3333333333333333" rx="3" ry="3" transform="rotate(30 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.4166666666666667" rx="3" ry="3" transform="rotate(60 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.5" rx="3" ry="3" transform="rotate(90 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.5833333333333334" rx="3" ry="3" transform="rotate(120 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.6666666666666666" rx="3" ry="3" transform="rotate(150 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.75" rx="3" ry="3" transform="rotate(180 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.8333333333333334" rx="3" ry="3" transform="rotate(210 50 50)" width="25" x="72" y="47"></rect><rect fill="#555555" height="6" opacity="0.9166666666666666" rx="3" ry="3" transform="rotate(240 50 50)" width="25" x="72" y="47"></rect></svg>
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
                    <!--
                    <div class='nav-link-item col-2' id='inbox-notification'>
                        <span class='message-notif'>
                            <a href="{%url 'inbox_list' %}">
                                <svg aria-label="Share Post" fill="#262626" height="24" viewBox="0 0 48 48" width="24">
                                <path d="M47.8 3.8c-.3-.5-.8-.8-1.3-.8h-45C.9 3.1.3 3.5.1 4S0 5.2.4 5.7l15.9 15.6 5.5 22.6c.1.6.6 1 1.2 1.1h.2c.5 0 1-.3 1.3-.7l23.2-39c.4-.4.4-1 .1-1.5zM5.2 6.1h35.5L18 18.7 5.2 6.1zm18.7 33.6l-4.4-18.4L42.4 8.6 23.9 39.7z"></path>
                                </svg>     
                                <span class='inbox-notif-numbers'>               
                                </span>               
                            </a>
                        </span>
                    </div>
                    -->
                    <div id='nav-explore' class='nav-link-item col-2' id='explore'>
                        <a href="explore.php">
                            <svg aria-label="Find People" class="_8-yf5 " fill="#262626" height="22" viewBox="0 0 48 48" width="22"><path clip-rule="evenodd" d="M24 0C10.8 0 0 10.8 0 24s10.8 24 24 24 24-10.8 24-24S37.2 0 24 0zm0 45C12.4 45 3 35.6 3 24S12.4 3 24 3s21 9.4 21 21-9.4 21-21 21zm10.2-33.2l-14.8 7c-.3.1-.6.4-.7.7l-7 14.8c-.3.6-.2 1.3.3 1.7.3.3.7.4 1.1.4.2 0 .4 0 .6-.1l14.8-7c.3-.1.6-.4.7-.7l7-14.8c.3-.6.2-1.3-.3-1.7-.4-.5-1.1-.6-1.7-.3zm-7.4 15l-5.5-5.5 10.5-5-5 10.5z" fill-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                    <div id='nav-profile' class="dropdown nav-link-item col-2">
                        <a  href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            <img class='rounded-circle' src="<?php echo $photo ?>" style='width: 25px; height: 25px;'>
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

                <!-- jQuery first, then Popper.js, then Bootstrap JS -->
                <script src="static/js/base.js"></script>                
                <div class="row col-11 col-md-9 mt-5 pt-4">
                    <div class='card col-3 rounded-0 border-right-0 p-0 d-none d-md-inline'>
                        <ul type='none' class='p-0'>
                            <a href="edit.php"><li class='p-3 pl-4' style="border-left:2px solid black; font-weight:600;">Edit Profile</li></a>
                            <a href="change_password.php"><li class='p-3 pl-4'>Change Password</li></a>
                        </ul>
                    </div>
                    <div class="card col-12 col-md-9 p-0 p-md-5 rounded-0 mx-auto">
                        <div class="card-content col-12 pl-lg-5 p-0">
                            <form method="POST" class="pr-5 mr-5" enctype="multipart/form-data">
                                <div class='mb-3' style="font-size: larger;">
                                    <img class='rounded-circle col-3' src="<?php echo $photo; ?>" alt='' style="width: 85px;">
                                    <?php echo $username ?>
                                </div>
                                <div class='mb-3'>
                                    Change Profile:
                                    <input type="file" name="photo" accept="image/*" id="id_photo">
                                </div>
                                <div class='mb-4 text-start d-flex justify-content-end'>
                                    <label><b>Name</b></label>
                                    <input type="text" name="fullname" value="<?php echo $fullname ?>" class="user-edit-form" placeholder="Fullname">
                                </div>
                                <div class='mb-4 text-start d-flex justify-content-end'>
                                    <label><b>Username</b></label>
                                    <input type="text" name="username" value="<?php echo $username ?>" class="user-edit-form" placeholder="Username">
                                </div>
                                <div class='mb-4 text-start d-flex justify-content-end'>
                                    <label><b>Email</b></label>
                                    <input type="text" name="email" value="<?php echo $email ?>" class="user-edit-form" placeholder="Email">
                                </div>
                                <div class='mb-4 text-start d-flex justify-content-end'>
                                    <label><b>Website</b></label>
                                    <input type="text" name="website" value="<?php echo $website ?>" class="user-edit-form" placeholder="Website">
                                </div>
                                <div class='mb-4 text-start d-flex justify-content-end'>
                                    <label><b>Bio</b></label>
                                    <input type="text" name="bio" value="<?php echo $bio ?>" class="user-edit-form" placeholder="Bio">
                                </div>
                                <div class='mb-4 text-start d-flex justify-content-end'>
                                    <label><b>Phone</b></label>
                                    <input type="text" name="phone" value="<?php echo $phone ?>" class="user-edit-form" placeholder="Phone">
                                </div>
                                <button type="submit" class="btn" style="background-color: #0095f6;"><b style="color: #fff;">Submit</b></button>
                            </form>
                        </div>
                    </div>
                </div>
    
                <footer class='col-12 mb-2 mt-5 justify-content-center' style="font-size: 0.75rem; color:#8e8e8e;">
                    <p class='text-center'style='cursor:pointer;'> About  &emsp;Blog  &emsp;Jobs &emsp; Help &emsp; API  &emsp;Privacy &emsp; Terms &emsp; Top Accounts &emsp; Hashtags &emsp;Locations</p>
                    <p class='text-center'style='cursor:pointer;'> English  © 2021 Instagram from Facebook</p>
                </footer>
                <style>
                    @media (max-width: 768px) { 
                        body, .card {
                            background-color: white !important;
                        }
                    }
                </style>
            </div>
        </main>

        <script>
            const loader = document.querySelector(".loader");
            window.onload = function () {
                setTimeout(function () {
                    loader.style.opacity = "0";
                    loader.style.display = "none";
                }, 250);
            }
        </script>

    </body>
</html>