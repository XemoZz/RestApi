<?php
session_start();
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!isset($_COOKIE["token"])) {   
    header("Location: index.php");
    exit;
}

$key = "login_key";
$token = $_COOKIE["token"];


$headers = new stdClass();
$decoded = JWT::decode($token, new Key($key, 'HS256'), $headers);

if (isset($_POST['logout'])) {    
    setcookie("token", "", time() - 3600, "/");
    header("Location: index.php");
    exit;
}

$db = new mysqli('127.0.0.1', 'root', '', 'rest_api');
$query = "SELECT * FROM users WHERE id = '".$decoded->user_id."'";
$result = $db->query($query);


$query = "SELECT * FROM films";
$result = $db->query($query);

echo "<table>";
echo "<tr><th>Film ID</th><th>Title</th><th>Genre</th><th>Year of Production</th><th>Rating</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['film_id'] . "</td>";
    echo "<td>" . $row['tytul'] . "</td>";
    echo "<td>" . $row['gatunek'] . "</td>";
    echo "<td>" . $row['rok_produkcji'] . "</td>";
    echo "<td>" . $row['ocena'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo '<form method="post" action="">
        <input type="submit" name="logout" value="Logout">
        </form>';


$db->close();
?>
