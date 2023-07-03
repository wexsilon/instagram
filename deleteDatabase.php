<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "instagram";
$charset = "utf8";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

/*
$pdoConnect = new PDO("mysql:host=$host;charset=$charset", $username, $password, $opt);
$pdoStmtCreateDB = $pdoConnect->query("CREATE DATABASE IF NOT EXISTS $dbname");
$pdoConnect = null;
*/

$pdoConnect = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password, $opt);

$deleteQuery = "DROP DATABASE $dbname;";

$pdoConnect->query($deleteQuery);