<?php
session_start();

if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    $_SESSION["logged_in"] = false;
    unset($_SESSION["username"]);
    unset($_SESSION["user_id"]);    
    http_response_code(302);
    header("Location: index.php");
    echo "<html><body><a href='login.php'>click me</a></body></html>";
} else {
    echo "You aren't even logged in!";
}
?>