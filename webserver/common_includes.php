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
    http_response_code(302);
    header("Location: $loc");
    echo "<html><body><a href='$loc'>click me</a></body></html>";
}
?>