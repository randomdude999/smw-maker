<?php
$mysqli = new mysqli("localhost", "root", null, "smwmaker");
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
    <p>Logging in takes 2 steps: first, enter your SMWC username and you'll be sent a PM on the site containing your token. Then enter your token to log in. If you need, then you can revoke the token by clicking the link in the PM.</p>
    <form action="send_token.php" method="post">Send token: <input name="username" placeholder="SMWC username"><input type="submit" value="Send token"></form>
    <form action="login_with_token.php" method="post">Login with token: <input name="token" placeholder="Login token"><input type="submit" value="Log in"></div>
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