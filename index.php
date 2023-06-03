<?php
  $servername = "localhost";
  $username = "root";
  $password = "root";
  $dbname = "archery_db";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Initialize empty results array
  $results = [];

  // Check if form was submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archer_id = $_POST['archer_id'];

    $sql = "SELECT round_name, stage_id, stage_total FROM stage_score WHERE archer_id = ? ORDER BY round_name, stage_id";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('i', $archer_id); // 'i' means it's an integer
    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result) {
      die("Query failed: " . $conn->error);
    }

    // Process the query result
    while ($row = $result->fetch_assoc()) {
      $results[] = $row;
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Archery Scores</title>
    <style>
      body {
        font-family: Arial, Helvetica, sans-serif;
        padding: 20px;
        background-color: #f4f4f4;
      }

      form {
        margin-bottom: 20px;
      }

      input[type="number"] {
        padding: 5px;
        margin-right: 10px;
      }

      button {
        padding: 5px 10px;
        background-color: #4CAF50;
        border: none;
        color: white;
      }

      button:hover {
        background-color: #45a049;
      }

      h3 {
        color: #333;
        margin-top: 20px;
      }

      p {
        background-color: #fff;
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
        box-shadow: 1px 1px 3px rgba(0,0,0,0.1);
      }
    </style>
  </head>
  <body>
    <form method="post">
      <label for="archer_id">Archer ID: </label>
      <input type="number" id="archer_id" name="archer_id" required>
      <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)) : ?>
      <?php $current_round = ''; ?>
      <?php foreach ($results as $row) : ?>
        <?php if ($row['round_name'] != $current_round) : ?>
          <h3><?php echo $row['round_name']; ?></h3>
          <?php $current_round = $row['round_name']; ?>
        <?php endif; ?>
        <p>
          Stage: <?php echo $row['stage_id']; ?>, Score: <?php echo $row['stage_total']; ?>
        </p>
      <?php endforeach; ?>
    <?php endif; ?>
  </
