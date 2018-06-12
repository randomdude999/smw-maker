<?php
include 'common_includes.php';
session_start();

if(get($_SESSION["logged_in"], false) === false) {
	die("Not logged in.");
}
?>
<html>
<head>
	<title>Upload - SMW Maker</title>
</head>
<body>
	<p>Upload level</p>
	<form action="upload_.php" enctype="multipart/form-data" method="POST">
		<input type="hidden" name="MAX_FILE_SIZE" value="2097152"> <!-- mirroring PHP's default upload_max_filesize -->
		<label for="lvlname">Level name:</label> <input type="text" name="lvlname" placeholder="Level name" required maxlength="255"><br>
		<label for="difficulty">Difficulty:</label> <input type="range" name="difficulty" min=0 max=3 step=1 id="dif"><span id="diftxt"></span><br>
		<label for="mainfile">Main level:</label> <input type="file" name="mainfile" accept=".mwl" required><br>
		<label for="hassub">Has sublevel:</label> <input type="checkbox" name="hassub" id="hassub"><br>
		<label for="subfile">Sublevel:</label> <input type="file" name="subfile" id="subfile" accept=".mwl"><br>
		<input type="submit" value="Upload">
	</form>
	<script>
		var difficulties = [
			"Easy",
			"Normal",
			"Hard",
			"Kaizo"
		];
		document.getElementById("dif").oninput = function() {
			document.getElementById("diftxt").innerHTML = difficulties[document.getElementById("dif").value];
		};
		document.getElementById("dif").oninput();
		document.getElementById("hassub").onchange = function() {
			document.getElementById("subfile").disabled = !document.getElementById("hassub").checked;
		}
		document.getElementById("hassub").onchange();
	</script>
</body>
</html>
