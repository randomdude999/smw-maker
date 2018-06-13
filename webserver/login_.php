<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["token"])) {
        http_response_code(400);
        echo "error: no token supplied";
        return;
    }
    $token = $_POST["token"];
    $res = sql_prepared_exec($mysqli, "SELECT name, id, smwc_id, token FROM users WHERE token = ?", "s", $token);
    if($res === NULL)
        die("MySQL error: ".$mysqli->error);
    if ($res->num_rows == 0) {
        redirect("login.php?errmsg=invalid_token");
        return;
    }
    $row = $res->fetch_array();
    $_SESSION["logged_in"] = TRUE;
    $_SESSION["username"] = $row["name"];
    $_SESSION["smwc_id"] = $row["smwc_id"];
    $_SESSION["user_id"] = $row["id"];
    redirect("index.php");
} else {
    redirect("login.php");
}
