<?php
function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function go_back_with_msg($msg) {
    http_response_code(302);
    header("Location: login.php?errmsg=$msg");
    echo "<html><body><a href='login.php?errmsg=$msg'>click me</a></body></html>";
}

$mysqli = new mysqli("localhost", "root", null, "smwmaker");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        http_response_code(400);
        echo "error: no username supplied";
        return;
    }
    $uname = $_POST["username"];
    $mysqli->prepare("SELECT id, name, token FROM users LEFT JOIN login_tokens ON login_tokens.user_id = id WHERE name = ?");
    $stmt->bind_param("s", $uname);
    if(!$stmt->execute()) {
        echo "MySQL error: ".$mysqli->error;
    } else {
        $res = $stmt->get_result();
        if($res->num_rows > 0) {
            // already exists in DB

        }
    }
} else {
    http_response_code(400);
    echo "error: invalid request method";
}
?>