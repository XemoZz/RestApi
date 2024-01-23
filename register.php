<?php

$db = new mysqli('127.0.0.1', 'root', '', 'rest_api');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imie = $db->real_escape_string($_POST['imie']);
    $nazwisko = $db->real_escape_string($_POST['nazwisko']);
    $login = $db->real_escape_string($_POST['login']);
    $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
    $email = $db->real_escape_string($_POST['email']);
    $telefon = $db->real_escape_string($_POST['telefon']);
    $czyAdmin = 0; 

    $query = "INSERT INTO users (imie, nazwisko, login, haslo, email, telefon, czyAdmin) VALUES ('$imie', '$nazwisko', '$login', '$haslo', '$email', '$telefon', '$czyAdmin')";

    if ($db->query($query)) {
        echo "Rejestracja przebiegła pomyślnie!";
    } else {
        echo "Błąd: " . $db->error;
    }
}
$db->close();
?>

<form method="post" action="register.php">
    Name: <input type="text" name="imie"><br>
    Surname: <input type="text" name="nazwisko"><br>
    Login: <input type="text" name="login"><br>
    Password: <input type="password" name="haslo"><br>
    Email: <input type="email" name="email"><br>
    Phone: <input type="text" name="telefon"><br>
    <input type="submit" value="Zarejestruj się">
</form>

<p><a href="index.php">Login</a></p>
