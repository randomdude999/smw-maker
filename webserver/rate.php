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
$lvlid = intval($_POST["lvlid"], 10);
if(!ctype_digit($_POST["rating"]))
	die("invalid rating");
$rating = intval($_POST["rating"], 10);
if($rating > 5 || $rating == 0)
	die("invalid rating");
if(!is_logged_in())
	die("not logged in");
$res = sql_prepared_exec($mysqli, "SELECT author FROM levels WHERE id = ?", "i", $lvlid);
if($res === NULL)
	die("MySQL error: ".$mysqli->error);
if($res->num_rows === 0)
	die("That level doesn't even exist");
$author = $res->fetch_array()["author"];
if($author === $_SESSION['user_id'])
	die("Can't rate your own levels!");
if(NULL === sql_prepared_exec($mysqli,
	"INSERT INTO ratings (levelId, userId, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating=?",
	"iiii", $lvlid, $author, $rating, $rating))
	die("MySQL error: ".$mysqli->error);
redirect("index.php");
