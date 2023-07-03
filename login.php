<?php
session_start();
 
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("Location: profile.php", true, 301);
    exit;
}
 

$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];
$pdoObj = new PDO("mysql:host=localhost;dbname=instagram;charset=utf8", 'root', '', $opt);


$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password, photo FROM users WHERE username = ?";
        $stmt = $pdoObj->prepare($sql);
        if($stmt){
            if($stmt->execute([$username])) {
                if($stmt->rowCount() == 1) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($user) { 
                        $id = $user['id'];
                        $username = $user['username'];
                        $hashed_password = $user['password'];
                        $photo = $user['photo'];
                        if(password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["photo"] = $photo;
                            header("Location: profile.php", true, 301);
                            exit;
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else{
                $login_err = "Oops! Something went wrong. Please try again later.";
            }
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
            .card{
                background-color: white !important;
                border:1px solid #e8e8e8 !important;
            }
        </style>
    </head>
    <body>    
        <div class='row col-xs-12 col-lg-10 mt-5 pl-sm-5 pr-sm-5 p-0' style="margin: 0 auto;">
            <div class="phone-side-content d-none d-lg-inline col-5 ml-5 p-0 pl-2 mr-0">
                <img src="static/img/phone.png" alt="phone image" style="height:35rem;">
            </div>
            <div class='signup-content text-center col-xs-12 col-lg-5 m-md-0 ml-sm-5 m-xs-0'>
                <div class='card p-4 pt-4'>
                    <p><img src="static/img/logo.png" alt="logo" style="width:170px"></p>  
                    
                    <ul class="errorlist nonfield">
                        <li><?php echo $username_err ?></li>
                        <li><?php echo $password_err ?></li>
                        <li><?php echo $login_err ?></li>
                    </ul>
                    
                    <form method="post" action="login.php">
                        <input type="text" name="username" class="signup-form" placeholder="Username" />
                        <input type="password" name="password" class="signup-form" placeholder="Password" />
                        <input type="submit" class="btn btn-primary col-11 mt-2 p-1" value="Log In" style="font-size: 0.9rem; font-weight: 600; background-color:#0095f6; " />
                    </form>
                </div>
                <div class='card text-center mt-3 p-4'>
                    <small>Don't have an account? <a href="signup.php" style="color: #0095f6 !important">Sign Up</a></small>
                </div>
            </div>
        </div>
    </body>
</html>



