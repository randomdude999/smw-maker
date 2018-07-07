<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();
if(!is_admin()) {
	echo "What do you want from here?";
	echo "<a href='index.php'>back</a>";
	return;
}
if(empty($_GET["action"])) {
	die("No action");
}
if(empty($_GET["id"])) {
	die("No id");
}

$act = $_GET["action"];
if($act === "accept") {
	if(NULL === sql_prepared_exec($mysqli, "UPDATE levels SET verified = 1 WHERE id = ?", "i", intval($_GET["id"]))) {
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	}
	redirect("index.php");
}
if($act === "delete") {
	if(NULL === sql_prepared_exec($mysqli, "DELETE FROM levels WHERE id = ?", "i", intval($_GET["id"]))) {
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	}
	redirect("index.php");
}
