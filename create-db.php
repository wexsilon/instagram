<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "instagram";
$charset = "utf8";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];


$pdoConnect = new PDO("mysql:host=$host;charset=$charset", $username, $password, $opt);
$pdoStmtCreateDB = $pdoConnect->query("CREATE DATABASE IF NOT EXISTS $dbname");
$pdoConnect = null;


$pdoConnect = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password, $opt);


$createTableUsersQuery = "CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    photo TEXT DEFAULT 'static/media/default.jpg',
    fullname TEXT NOT NULL,
    bio TEXT,
    website TEXT,
    phone TEXT,
    email TEXT NOT NULL,
    following_count INT UNSIGNED DEFAULT 0,
    follower_count INT UNSIGNED DEFAULT 0,
    post_count INT UNSIGNED DEFAULT 0
)";
$pdoConnect->query($createTableUsersQuery);

$createTablePostsQuery = "CREATE TABLE IF NOT EXISTS posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    photo TEXT NOT NULL,
    comment_count INT UNSIGNED DEFAULT 0,
    like_count INT UNSIGNED DEFAULT 0,
    username TEXT NOT NULL,
    caption TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";
$pdoConnect->query($createTablePostsQuery);

$createTableFollowsQuery = "CREATE TABLE IF NOT EXISTS follows (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner TEXT NOT NULL,
    reciever TEXT NOT NULL
)";
$pdoConnect->query($createTableFollowsQuery);

$createTableCommentsQuery = "CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author TEXT NOT NULL,
    textt TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    pid INT UNSIGNED
)";
$pdoConnect->query($createTableCommentsQuery);

$createTableLikesQuery = "CREATE TABLE IF NOT EXISTS likes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username TEXT NOT NULL,
    pid INT UNSIGNED
)";
$pdoConnect->query($createTableLikesQuery);

$createTableSavesQuery = "CREATE TABLE IF NOT EXISTS saves (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username TEXT NOT NULL,
    pid INT UNSIGNED
)";
$pdoConnect->query($createTableSavesQuery);