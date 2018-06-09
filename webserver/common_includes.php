<?php
function connect_db() {
    $conn = @new mysqli("localhost", "root", null, "smwmaker");
    if($conn->connect_error) {
        echo "Error: could not connect to mysql ($conn->connect_errno: $conn->connect_error)";
        exit();
    }
    return $conn;
}

function redirect($loc) {
    http_response_code(303); // use 303 to make sure the request method is changed to GET
    header("Location: $loc");
    echo "<html><body><a href='$loc'>click me</a></body></html>";
}

function get(&$var, $default=null) {
    return isset($var) ? $var : $default;
}