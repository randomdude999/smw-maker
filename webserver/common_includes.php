<?php
function connect_db() {
    return new mysqli("localhost", "root", null, "smwmaker");
};

function redirect($loc) {
    http_response_code(302);
    header("Location: $loc");
    echo "<html><body><a href='$loc'>click me</a></body></html>";
};
?>