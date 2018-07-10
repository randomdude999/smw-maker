<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();
if(!is_admin()) {
	echo "What do you want from here?";
	echo "<a href='index.php'>back</a>";
	return;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>User list - SMW Maker</title>
	<style>
		table, th, td {
			border: 1px solid;
		}
		table {
			border-collapse: collapse;
		}
	</style>
</head>
<body>
	<table>
		<tr><th>ID</th><th>Name</th><th>SMWC link</th><th>Banned</th><th>Admin</th></tr>
		<?php
$users = $mysqli->query("SELECT id, name, smwc_id, banned, admin FROM users;");
foreach($users as $user) {
	echo "<tr><td>$user[id]</td><td>".htmlspecialchars($user["name"])."</td>";
	echo "<td><a href='https://smwc.me/u/$user[smwc_id]'>$user[smwc_id]</a></td>";
	if($user['banned']) {
		echo "<td><input type=checkbox disabled checked><a href='moderate.php?id=$user[id]&action=unban'>Unban</a></td>";
	} else {
		echo "<td><input type=checkbox disabled unchecked><a href='moderate.php?id=$user[id]&action=ban'>Ban</a></td>";
	}
	echo "<td><input type=checkbox disabled ".($user['admin'] ? "checked" : "unchecked")."></td>";
	echo "</tr>";
}
		?>
</body>
</html>
