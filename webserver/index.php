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
    <p>Usage: Create a level in Lunar Magic (may have one sublevel too), then register and upload it.</p>
<?php if (is_logged_in()): ?>
    <p>Logged in as <a href='https://smwc.me/u/<?= htmlspecialchars($_SESSION["smwc_id"]) ?>'><?= htmlspecialchars($_SESSION["username"]) ?></a></p>
    <form action='logout.php' method='POST'><input type='submit' value='Log out'></form>
    <p><a href='upload.php'>Upload level</a></p>
<?php else: ?>
    <p><a href='login.php'>Log in / register</a></p>
<?php endif; ?>
    <?php if (isset($_GET["show_waiting"])): ?>
      <a href="play.php?unverified">Play random selection of 10 levels (including unmoderated ones!)</a>
    <?php else: ?>
      <a href="play.php">Play random selection of 10 levels</a>
    <?php endif; ?>
    <p>Browse levels: (click on the name to play)</p>
<?php

if(isset($_GET["show_waiting"])) {
  echo "<p><a href='index.php'>Hide unmoderated levels</a></p>";
  $get_display_level_data_query = "
  SELECT levels.id,
         levels.name,
         levels.difficulty,
         levels.verified,
         users.name AS author,
         users.smwc_id AS author_id,
         AVG(rating) AS avg_rating,
         COUNT(rating) AS rating_count
    FROM levels
         LEFT JOIN
         ratings ON levels.id = ratings.levelId
         LEFT JOIN
         users ON levels.author = users.id
   GROUP BY levels.id
   ORDER BY levels.id DESC;
  ";
} else {
  echo "<p><a href='index.php?show_waiting'>Show unmoderated levels</a></p>";
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
      WHERE levels.verified = 1
   GROUP BY levels.id
   ORDER BY levels.id DESC;
  ";
}
$difficulties = [
    "Easy",
    "Normal",
    "Hard",
    "Kaizo"
];

$res = $mysqli->query($get_display_level_data_query);
if(!$res) {
    echo "Error querying MySQL: ".htmlspecialchars($mysqli->error);
    return;
}
if($res->num_rows === 0) {
    echo "No levels found.";
}

foreach($res as $row): ?>
    <div class='lvl'><a href='play.php?id=<?= $row['id'] ?>'><?= htmlspecialchars($row['name']) ?></a><br>
    Created by <a href="https://smwc.me/u/<?= $row['author_id'] ?>"><?= htmlspecialchars($row['author']) ?></a><br>
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
    <?php if(isset($_GET["show_waiting"]) && is_admin() && $row["verified"] == 0): ?>
      <a href="moderate.php?id=<?= $row['id'] ?>&action=accept">Accept</a> |
      <a href="moderate.php?id=<?= $row['id'] ?>&action=delete">Reject</a>
    <?php elseif(is_admin()): ?>
      <a href="moderate.php?id=<?= $row['id'] ?>&action=delete">Delete</a>
    <?php endif; ?>
    </div>
<?php endforeach; ?>
  <p>Made by <a href="https://smwc.me/u/32552">randomdude999</a> - <a href="https://github.com/randomdude999/smw-maker">Source code</a> - <a href="thanks.html">Credits</a></p>
</body>
</html>
