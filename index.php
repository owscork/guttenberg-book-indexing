<!DOCTYPE html>
<html>
<head>
    <title>Gutenberg Books</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<h1>Gutenberg Books</h1>
<br>
<form action="index.php" method="post">
    Word 1: <input type="text" name="word"><br>
    <br>
    Word 2: <input type="text" name="word2"><br>
    <br>
    Search Type:
    <input type="radio" name="type" value="and">And
    <input type="radio" name="type" value="not">Without
    <br>
    <input type="submit">
</form>

<?php 
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "gutenberg";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect value of input field
    $word = $_POST['word'];
    $word2 = $_POST['word2'];
    $type = $_POST['type'];

    if (empty($word)) {
      echo "Search is empty";
    } else if (empty($word2)){
      searchIndex($word, $conn);
    } else if (!empty($type)) {
      if ($type == "and"){
        searchAnd($word, $word2, $conn);
      } else if ($type == "not"){
        searchNot($word, $word2, $conn);
      }
    }
}

function searchNot(string $word, string $word2, mysqli $conn){
  $term_search = "SELECT (id) FROM inverted WHERE word='" . $word . "'";
  $term_array = $conn -> query($term_search);
  $term_array2 = mysqli_fetch_array($term_array);
  $id1 = $term_array2["id"];

  $term_search = "SELECT (id) FROM inverted WHERE word='" . $word2 . "'";
  $term_array = $conn -> query($term_search);
  $term_array2 = mysqli_fetch_array($term_array);
  $id2 = $term_array2["id"];

  $sqlWordSearch = "SELECT * FROM document WHERE term_id='" . $id1 . "'";
  $result = $conn -> query($sqlWordSearch);
  
  if ($result->num_rows > 0) {
    // output data of each row
      while($row = $result->fetch_assoc()) {
        $next_location = $row["pos"] + 1;
        $book_num = $row["book_num"];

        $sqlCall = "SELECT * FROM document WHERE term_id!='" . $id2 . "' AND book_num=" . $book_num . " AND pos=" . $next_location;
        $result2 = $conn -> query($sqlCall);

        if ($result2->num_rows > 0) {
          $row2 = $result2->fetch_assoc(); 
          echo "Found at Book: " . $row["book_num"] . " In location: " . $row["pos"] . "<br>";
        } 
      }
    } 
}


function searchAnd(string $word, string $word2, mysqli $conn){
  $term_search = "SELECT (id) FROM inverted WHERE word='" . $word . "'";
  $term_array = $conn -> query($term_search);
  $term_array2 = mysqli_fetch_array($term_array);
  $id1 = $term_array2["id"];

  $term_search = "SELECT (id) FROM inverted WHERE word='" . $word2 . "'";
  $term_array = $conn -> query($term_search);
  $term_array2 = mysqli_fetch_array($term_array);
  $id2 = $term_array2["id"];

  $sqlWordSearch = "SELECT * FROM document WHERE term_id='" . $id1 . "'";
  $result = $conn -> query($sqlWordSearch);
  
  if ($result->num_rows > 0) {
    // output data of each row
      while($row = $result->fetch_assoc()) {
        $next_location = $row["pos"] + 1;
        $book_num = $row["book_num"];

        $sqlCall = "SELECT * FROM document WHERE term_id='" . $id2 . "' AND book_num=" . $book_num . " AND pos=" . $next_location;
        $result2 = $conn -> query($sqlCall);

        if ($result2->num_rows > 0) {
          $row2 = $result2->fetch_assoc(); 
          echo "Found at Book: " . $row["book_num"] . " In location: " . $row["pos"] . "<br>";
        } 
      }
    } 
}

function searchIndex(string $word, mysqli $conn){
    $term_search = "SELECT (id) FROM inverted WHERE word='" . $word . "'";
    $term_array = $conn -> query($term_search);
    $term_array2 = mysqli_fetch_array($term_array);
    // add an if statement to handle booleans
    $term_id = $term_array2["id"];
    $sqlWordSearch = "SELECT * FROM document WHERE term_id='" . $term_id . "'";
    $result = $conn -> query($sqlWordSearch);
    if ($result->num_rows > 0) {
    // output data of each row
      while($row = $result->fetch_assoc()) {
        echo "Found at Book: " . $row["book_num"] . " In location: " . $row["pos"] . "<br>";
      }
    } else {
    echo "0 results";
    }
};

$conn->close();
?> 

</body>
</html>