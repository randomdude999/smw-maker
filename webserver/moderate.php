<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();
if(!is_admin()) {
	echo "What do you want from here?";
	echo "<a href='index.php'>back</a>";
	return;
}
if(empty($_GET["action"])) {
	die("No action");
}
$act = $_GET["action"];
if(!in_array($act, ["accept", "reject", "delete"]))
	die("Invalid action");

if(empty($_GET["id"]))
	die("No id");
$id = intval($_GET["id"]);
if($id == 0) die("Invalid ID");

$res = sql_prepared_exec($mysqli, "SELECT name FROM levels WHERE id = ?", "i", $id);
if($res === NULL) die("MySQL error: ".htmlspecialchars($mysqli->error));
if($res->num_rows != 1) die("No such level");
$name = $res->fetch_array()["name"];

?>
<!DOCTYPE html>
<html>
<head>
	<title>Moderate - SMW Maker</title>
</head>
<body>
	<p>You are about to <?= $_GET["action"] ?> <b><?= htmlspecialchars($name) ?></b>.</p>
	<form action="moderate_.php" method="POST">
		<input type="hidden" name="id" value="<?= $id ?>">
		<input type="hidden" name="action" value="<?= $act ?>">
		<input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
		<textarea name="comment" placeholder="Comment..."></textarea><br>
		<input type="submit" value="Confirm">
	</form>
</body>
</html>
