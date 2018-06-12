<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();

if($_SERVER["REQUEST_METHOD"] !== "POST")
	redirect("index.php");
if(!isset($_POST["rating"]))
	die("no rating");
if(!isset($_POST["lvlid"]))
	die("no lvlid");
if(!ctype_digit($_POST["lvlid"]))
	die("invalid lvlid");
$lvlid = intval($_POST["lvlid"]);
if(!ctype_digit($_POST["rating"]))
	die("invalid rating");
$rating = intval($_POST["rating"]);
if($rating > 5 || $rating == 0)
	die("invalid rating");
if(!is_logged_in())
	die("not logged in");
$stmt = $mysqli->prepare("SELECT author FROM levels WHERE id = ?");
if($stmt === FALSE)
	die("MySQL error: ".$mysqli->error);
$stmt->bind_param("i", $lvlid);
if(!$stmt->execute())
	die("MySQL error: ".$mysqli->error);
$res = $stmt->get_result();
if($res->num_rows === 0)
	die("That level doesn't even exist");
$author = $res->fetch_array()["author"];
if($author === $_SESSION['user_id'])
	die("Can't rate your own levels!");
$stmt = $mysqli->prepare("INSERT INTO ratings (levelId, userId, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating=?");
if($stmt === FALSE)
	die("MySQL error: ".$mysqli->error);
$stmt->bind_param("iiii", $lvlid, $author, $rating, $rating);
if(!$stmt->execute())
	die("MySQL error: ".$mysqli->error);
redirect("index.php");