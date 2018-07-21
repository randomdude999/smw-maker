<?php
include 'common_includes.php';
$mysqli = connect_db();

$result = $mysqli->query("
	SELECT 	subject,
			users.smwc_id AS user_smwcid,
			users.name AS uname,
			action,
			comment,
			DATE_FORMAT(when_happened, '%Y-%m-%d %H:%i:%s') AS when_happened
		FROM moderation_log
		LEFT JOIN users ON userId=users.id
		ORDER BY when_happened DESC");
if($result === FALSE) die("MySQL error: ".htmlspecialchars($mysqli->error));
?>
<!DOCTYPE html>
<html>
<head>
	<title>Moderation log - SMW Maker</title>
	<style>
		table {
			border-collapse: collapse;
		}
		table, td, th {
			border: 1px solid black;
		}
	</style>
</head>
<body>
<p>(Current time: <?= date('Y-m-d H:i:s') ?>)
<table>
	<tr>
		<th>User</th>
		<th>Action</th>
		<th>Comment</th>
		<th>Time</th>
	</tr>
<?php
$actions = [
	"accept" => "Accepted",
	"reject" => "Rejected",
	"delete" => "Deleted"
];
foreach($result as $row) {
	echo "<tr><td>";
	echo "<a href='https://smwc.me/u/".$row["user_smwcid"]."'>".htmlspecialchars($row["uname"])."</a>";
	echo "</td><td>";
	echo $actions[$row["action"]]." \"".htmlspecialchars($row["subject"])."\"";
	echo "</td><td>";
	echo str_replace("\n", "<br>", htmlspecialchars($row["comment"]));
	echo "</td><td>";
	echo $row["when_happened"];
	echo "</td></tr>";
}
?>
</table>
<p><a href="index.php">back</a></p>
</body>
</html>
