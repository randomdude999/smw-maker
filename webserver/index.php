<?php
include 'common_includes.php';
$mysqli = connect_db();
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
    <p>So this is a thing i made. Basically kinda like mario maker except in SMW.</p>
<?php if (is_logged_in()): ?>
    <p>Logged in as <a href='https://smwc.me/u/<?= $_SESSION["username"] ?>'><?= $_SESSION["username"] ?></a></p>
    <form action='logout.php' method='POST'><input type='submit' value='Log out'></form>
    <p><a href='upload.php'>Upload level</a></p>
<?php else: ?>
    <p><a href='login.php'>Log in</a></p>
<?php endif; ?>
    <a href="play.php">Play random selection of 10 levels</a>
    <p>Browse levels: (click on the name to play)</p>
<?php

$get_display_level_data_query = "
SELECT levels.id,
       levels.name,
       levels.difficulty,
       users.name AS author,
       users.smwc_id AS author_id,
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
    return;
}
if($res->num_rows === 0) {
    echo "No levels found.";
}

foreach($res as $row): ?>
    <div class='lvl'><a href='play.php?id=<?= $row['id'] ?>'><?= $row['name'] ?></a><br>
    Created by <a href="https://smwc.me/u/<?= $row['author_id'] ?>"><?= $row['author'] ?></a><br>
    Difficulty: <?= $difficulties[$row["difficulty"]] ?>
    <?php if($row["avg_rating"]!==NULL): ?>
        <br>Rating: <?= number_format($row["avg_rating"],1) ?>/5
    <?php endif; ?>
    <?php if(is_logged_in()): ?>
      <br><form action="rate.php" method="POST">Rate:
          <input type="hidden" name="lvlid" value="<?= $row['id'] ?>">
          <input type="submit" name="rating" value="1">
          <input type="submit" name="rating" value="2">
          <input type="submit" name="rating" value="3">
          <input type="submit" name="rating" value="4">
          <input type="submit" name="rating" value="5">
      </form>
    <?php endif; ?>
    </div>
<?php endforeach; ?>
</body>
</html>
