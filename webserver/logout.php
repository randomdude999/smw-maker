<?php
include 'common_includes.php';
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(get($_SESSION["logged_in"], false) === true) {
	    $_SESSION["logged_in"] = false;
	    unset($_SESSION["username"]);
	    unset($_SESSION["user_id"]);
	    redirect("index.php");
	} else {
	    echo "You aren't even logged in!";
	}
} else {
	redirect("index.php");
}
