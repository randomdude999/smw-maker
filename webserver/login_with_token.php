<?php
$mysqli = new mysqli("localhost", "root", null, "smwmaker");
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["token"])) {
        http_response_code(400);
        echo "error: no token supplied";
        return;
    }
    $token = $_POST["token"];
    $stmt = $mysqli->prepare("SELECT users.name, user_id, token FROM login_tokens INNER JOIN users ON users.id = user_id WHERE token = ?");
    $stmt->bind_param("s", $token);
    if(!$stmt->execute()) {
        echo "MySQL error: ".$mysqli->error;
    } else {
        $res = $stmt->get_result();
        if ($res->num_rows == 0) {
            http_response_code(302);
            header("Location: login.php?errmsg=invalid_token");
            echo "<html><body><a href='login.php?errmsg=invalid_token'>click me</a></body></html>";
            return;
        }
        $row = $res->fetch_array();
        $_SESSION["logged_in"] = TRUE;
        $_SESSION["username"] = $row["name"];
        $_SESSION["user_id"] = $row["user_id"];
    }
} else {
    http_response_code(400);
    echo "error: invalid request method";
}
?>