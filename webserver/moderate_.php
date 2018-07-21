<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();
if(!is_admin()) {
	echo "What do you want from here?";
	echo "<a href='index.php'>back</a>";
	return;
}
if($_SERVER["REQUEST_METHOD"] !== "POST") die("Invalid request type");

if(empty($_POST["action"]))	die("No action");
if(empty($_POST["id"])) die("No id");

$act = $_POST["action"];
if($act === "accept") {
	if(NULL === sql_prepared_exec($mysqli, "UPDATE levels SET verified = 1 WHERE id = ?", "i", intval($_POST["id"]))) {
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	}
	if(NULL === sql_prepared_exec($mysqli,
			"INSERT INTO moderation_log (subject, userId, action, comment) VALUES (?, ?, ?, ?)",
			"siss", $_POST["name"], $_SESSION["user_id"], $act, get($_POST["comment"]))) {
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	}
	redirect("index.php");
} elseif($act === "delete" || $act === "reject") {
	if(NULL === sql_prepared_exec($mysqli, "DELETE FROM levels WHERE id = ?", "i", intval($_POST["id"])))
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	if(NULL === sql_prepared_exec($mysqli,
			"INSERT INTO moderation_log (subject, userId, action, comment) VALUES (?, ?, ?, ?)",
			"siss", $_POST["name"], $_SESSION["user_id"], $act, get($_POST["comment"]))) {
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	}
	redirect("index.php");
} elseif($act === "ban") {
	if(NULL === sql_prepared_exec($mysqli, "UPDATE users SET banned = 1 WHERE id = ?", "i", intval($_POST["id"]))) {
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	}
	redirect("userlist.php");
} elseif($act === "unban") {
	if(NULL === sql_prepared_exec($mysqli, "UPDATE users SET banned = 0 WHERE id = ?", "i", intval($_POST["id"]))) {
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	}
	redirect("userlist.php");
} else {
	die("Invalid action");
}
