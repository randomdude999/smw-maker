<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - SMW Maker</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <p>First, on SMWC, send a PM to randombot999 with the title "smwmaker verify" (no quotes) and any message (<a href="https://www.smwcentral.net/?p=pm&do=compose&user=34934&subject=smwmaker%20verify&text=smwmaker%20verify">or just click here</a>). The bot will reply with a token (may take up to 30 seconds). Enter this token here.</p>
    <form action="login_.php" method="post">Enter token: <input name="token" placeholder="Login token"><input type="submit" value="Log in"></div>
<?php
if (!empty($_GET["errmsg"])) {
    echo "<div class=error>";
    switch ($_GET["errmsg"]) {
        case 'invalid_token':
            echo "Error: the token is invalid.";
            break;
        default:
            echo "Unspecified error";
            break;
    }
    echo "</div>";
}
?>
</body>
</html>
