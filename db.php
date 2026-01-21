<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "cs109_machine";

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "DB connection failed: " . $mysqli->connect_error]);
    exit;
}

$mysqli->set_charset("utf8mb4");
