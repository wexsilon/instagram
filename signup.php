<?php
session_start();
 if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("Location: login.php", true, 301);
    exit;
}

$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);

$username = $password = $confirm_password = $fullname = $email = "";

$errors = array();

 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
   
    if(empty(trim($_POST["username"]))) {
        array_push($errors, "Please enter a username.");
    }
    elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        array_push($errors, "Username can only contain letters, numbers, and underscores.");
    }
    else{
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $pdoObj->prepare($sql);
        if ($stmt) {
            $param_username = trim($_POST["username"]);
            if ($stmt->execute([$param_username])) {
                if ($stmt->rowCount() == 1) {
                    array_push($errors, "This username is already taken.");
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                array_push($errors, "Oops! Something went wrong. Please try again later.");
            }
        }
    }


    if(empty(trim($_POST["fullname"]))) {
        array_push($errors, "Please enter a fullname.");
    }
    elseif(!preg_match("/^([a-zA-Z' ]+)$/", trim($_POST["fullname"]))) {
        array_push($errors, "Username can only contain letters.");
    }
    else {
        $fullname = trim($_POST['fullname']);
    }
    
    if(empty(trim($_POST["email"]))) {
        array_push($errors, "Please enter a email.");
    }
    elseif(!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Invalid email format.");
    }
    else {
        $email = trim($_POST['email']);
    }

    
    if(empty(trim($_POST["password"]))){
        array_push($errors, "Please enter a password.");
    }
    elseif(strlen(trim($_POST["password"])) < 8){
        array_push($errors, "Password must have atleast 8 characters .");
    }
    elseif(!preg_match('@[A-Z]@', trim($_POST["password"]))) {
        array_push($errors, "Password must have least one upper case letter.");
    }
    elseif(!preg_match('@[a-z]@', trim($_POST["password"]))) {
        array_push($errors, "Password must have least one lower case letter.");
    }
    elseif(!preg_match('@[0-9]@', trim($_POST["password"]))) {
        array_push($errors, "Password must have least one number.");
    }
    elseif(!preg_match('@[^\w]@', trim($_POST["password"]))) {
        array_push($errors, "Password must have least one special character.");
    }
    else {
        $password = trim($_POST["password"]);
    }
    
    if(empty(trim($_POST["confirm_password"]))){
        array_push($errors, "Please confirm password.");
    }
    else{
        $confirm_password = trim($_POST["confirm_password"]);
        if($password != $confirm_password){
            array_push($errors, "Password did not match.");
        }
        else {
            $password = password_hash($password, PASSWORD_DEFAULT);
        }
    }
    
    if(count($errors) == 0){
        $sql = "INSERT INTO users (username, password, fullname, email) VALUES ('$username', '$password', '$fullname', '$email')";
        if ($pdoObj->query($sql)) {
            header("Location: login.php", true, 301);
            exit;            
        }
        else {
            $server_err = "Oops! Something went wrong. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Instagram</title>

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

        <style>
            .card {
                background-color: white !important;
                border:1px solid #e8e8e8 !important;
            }
        </style>
    </head>
    <body>    
        <div class='row col-xs-12 col-lg-10 mt-5 pl-sm-5 pr-sm-5 p-0' style="margin: 0 auto;">
            <div class="phone-side-content d-none d-lg-inline col-5 mt-5 ml-5 p-0 pl-2 mr-0">
                <img src="static/img/phone.png" alt="phone image" style="height:35rem;">
            </div>
            <div class='signup-content text-center col-xs-12 col-lg-5 m-md-0 ml-sm-5 m-xs-0'>
                <div class='card p-4 pt-4'>
                    <p><img src="static/img/logo.png" alt="logo" style="width:170px"></p>  
                    <p style="color: #8e8e8e; font-size:medium; font-weight: 600;">Sign up to see photos and videos from your friends.</p>  
                    <?php
                    if (count($errors) > 0) {
                        echo '<ul class="errorlist nonfield">';
                        foreach ($errors as $err) {
                            echo "<li>$err</li>";
                        }
                        echo '</ul>';
                    }
                    ?>
                    <form method="post" action="signup.php">
                        <input type="text" name="username" value="" class="signup-form" placeholder="Username" />
                        <input type="text" name="fullname" value="" class="signup-form" placeholder="Full Name" />
                        <input type="email" name="email" value="" class="signup-form" placeholder="Email" />
                        <input type="password" name="password" value="" class="signup-form" placeholder="Password" />
                        <input type="password" name="confirm_password" value="" class="signup-form" placeholder="Password Again" />
                        <input type="submit" class="btn btn-primary col-11 mt-2 pt-1 pb-1" value="Sign up" style="font: 0.85rem; background-color:#0095f6;" />
                    </form>
                    <small class='col-11 text-muted mt-3 ml-3' style="font-size: 0.7rem;">By signing up, you agree to our <b> Terms , Data Policy and Cookies Policy </b>.</small>
                </div>
                <div class='card text-center mt-3 p-4'>
                    <small>Have an account? <a href="login.php" style="color: #0095f6 !important">Log In</a></small>
                </div>
            </div>
        </div>
    </body>
</html>
