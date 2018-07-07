<?php
include 'common_includes.php';
$mysqli = connect_db();
session_start();

function uploadCodeToMsg($code) {
	switch ($code) {
		case UPLOAD_ERR_OK:
			return "Upload was successful";
		case UPLOAD_ERR_INI_SIZE:
			return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
		case UPLOAD_ERR_FORM_SIZE:
			return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
		case UPLOAD_ERR_PARTIAL:
			return "The uploaded file was only partially uploaded";
		case UPLOAD_ERR_NO_FILE:
			return "No file was uploaded";
		case UPLOAD_ERR_NO_TMP_DIR:
			return "Missing a temporary folder";
		case UPLOAD_ERR_CANT_WRITE:
			return "Failed to write file to disk";
		case UPLOAD_ERR_EXTENSION:
			return "File upload stopped by extension";
		default:
			return "Unknown upload error"; 
	}
}

if($_SERVER["REQUEST_METHOD"] === "POST") {
	if(get($_SESSION["logged_in"], false) === false)
		die("Not logged in.");
	if(!isset($_POST["lvlname"]) || !isset($_POST["difficulty"]))
		die("Invalid request.");
	$difficulty = $_POST["difficulty"];
	$level_name = $_POST["lvlname"];
	$hassub = isset($_POST["hassub"]);
	if(empty($_FILES["mainfile"]))
		die("No main file provided");
	if($hassub && empty($_FILES["subfile"]))
		die("No sublevel file provided");
	if(strlen($level_name) > 255)
		die("lvlname too long");
	if(!ctype_digit($difficulty))
		die("Invalid difficulty");
	if(intval($difficulty, 10) > 3) // it can't be <0 because that would require non-digit chars
		die("Invalid difficulty");
	$difficulty = intval($difficulty, 10);
	if($_FILES["mainfile"]["error"] !== 0)
		die(uploadCodeToMsg($_FILES["mainfile"]["error"]));
	if($hassub && $_FILES["subfile"]["error"] !== 0)
		die(uploadCodeToMsg($_FILES["subfile"]["error"]));
	exec(realpath("..")."/checkmwl.py ".escapeshellarg($_FILES["mainfile"]["tmp_name"])." 2>&1", $output, $retcode);
	if($retcode !== 0) {
		echo "Error running MWL checker. This doesn't look like a valid MWL file. <pre>";
		echo htmlspecialchars(join("\n", $output));
		echo "</pre>";
		return;
	}
	if($hassub) {
		exec(realpath("..")."/checkmwl.py ".escapeshellarg($_FILES["subfile"]["tmp_name"])." 2>&1", $output, $retcode);
		if($retcode !== 0) {
			echo "Error running MWL checker. This doesn't look like a valid MWL file. <pre>";
			echo htmlspecialchars(join("\n", $output));
			echo "</pre>";
			return;
		}
	}
	if(NULL === sql_prepared_exec($mysqli,
		"INSERT INTO levels (name, author, difficulty) VALUES (?, ?, ?)",
		"sii", $level_name, $_SESSION["user_id"], $difficulty))
		die("MySQL error: ".htmlspecialchars($mysqli->error));
	$id = $mysqli->insert_id;
	rename($_FILES["mainfile"]["tmp_name"], "../levels/${id}_main.mwl");
	if($hassub) rename($_FILES["subfile"]["tmp_name"], "../levels/${id}_sub.mwl");
	redirect("index.php");
} else {
	redirect("upload.php");
}
