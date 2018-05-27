<?php
include 'common_includes.php';
session_start();

if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    $_SESSION["logged_in"] = false;
    unset($_SESSION["username"]);
    unset($_SESSION["user_id"]);
    redirect("index.php");
} else {
    echo "You aren't even logged in!";
}
?>