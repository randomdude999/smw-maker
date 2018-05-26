<?php
$mysqli = new mysqli("localhost", "root", null, "smwmaker");
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SMW Maker</title>
    <style>
        .lvl {
            border: 1px solid black;
            display: inline-block;
            margin: 5px;
            padding: 3px 5px;
        }
    </style>
</head>
<body>
    <p>So this is a thing i made. Basically like mario maker except in SMW.</p>
<?php
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) {
    echo "<p>Logged in as <a href='https://smwc.me/u/".$_SESSION["username"]."'>".$_SESSION["username"]."</a></p>";
    echo "<p><a href='logout.php'>Log out</a></p>";
    echo "<p><a href='upload.php'>Upload level</a></p>";
} else {
    echo "<p><a href='login.php'>Log in</a></p>";
}
?>
    <a href="/10lvl.php">Play random selection of 10 levels</a>
    <p>Browse levels: (click on the name to play)</p>
<?php

$get_display_level_data_query = "
SELECT levels.id,
       levels.name,
       levels.difficulty,
       users.name AS author,
       AVG(rating) AS avg_rating,
       COUNT(rating) AS rating_count
  FROM levels
       LEFT JOIN
       ratings ON levels.id = ratings.levelId
       LEFT JOIN
       users ON levels.author = users.id
 GROUP BY levels.id;
";
$difficulties = [
    "Easy",
    "Normal",
    "Hard",
    "Kaizo"
];

$res = $mysqli->query($get_display_level_data_query);
if(!$res) {
    echo "Error querying MySQL: $mysqli->error"; 
}
foreach($res as $row) {
    echo "<div class='lvl'><a href='1lvl.php?id=$row[id]'>$row[name]</a><br>";
    echo "Created by <a href='https://smwc.me/u/$row[author]'>$row[author]</a><br>";
    echo "Difficulty: ".$difficulties[$row["difficulty"]];
    if($row["avg_rating"]!==NULL) {
        echo "<br>Rating: ".number_format($row["avg_rating"],1)."/5";
    }
    echo "</div>";
}
?>
</body>
</html>
