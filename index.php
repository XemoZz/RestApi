<?php
session_start();
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$db = new mysqli('127.0.0.1', 'root', '', 'rest_api');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $db->real_escape_string($_POST['login']);
    $haslo = $_POST['haslo'];

    $query = "SELECT id, haslo, czyAdmin FROM users WHERE login = '$login'";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($haslo, $row['haslo'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['userid'] = $row['id'];

            $key = "login_key";
            $payload = array(
                "user_id" => $row['id'],                                
            );

            $jwt = JWT::encode($payload, $key, 'HS256');
            setcookie("token", $jwt, time() + (86400 * 30), "/"); // 30 dni
            if($row['czyAdmin'] == 1){
                header("Location: admin_page.php"); 
            }
            else{
                header("Location: user_page.php");
            }
        } else {
            echo "Nieprawidłowe hasło!";
        }
    } else {
        echo "Nie znaleziono użytkownika!";
    }
}
$db->close();
?>

<form method="post" action="index.php">
    Login: <input type="text" name="login"><br>
    Password: <input type="password" name="haslo"><br>
    <input type="submit" value="Zaloguj się">
</form>

<p><a href="register.php">Register</a></p>