<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(get($_SESSION["logged_in"], false) === false) {
		echo "Not logged in.";
		return;
	}
	# TODO
} else {
	redirect("upload.php");
}