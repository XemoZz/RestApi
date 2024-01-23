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


$db = new mysqli('127.0.0.1', 'root', '', 'rest_api');
$query = "SELECT czyAdmin FROM users WHERE id = '".$decoded->user_id."'";
$result = $db->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['czyAdmin'] != 1) { 
        header("Location: user_page.php");
        exit;
    }
} else {
    header("Location: user_page.php");
    exit;
}


if (isset($_POST['logout'])) {    
    setcookie("token", "", time() - 3600, "/");
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['add'])) {
        $tytul = $db->real_escape_string($_POST['tytul']);
        $gatunek = $db->real_escape_string($_POST['gatunek']);
        $rok_produkcji = intval($_POST['rok_produkcji']);
        $ocena = floatval($_POST['ocena']);

        $add_query = "INSERT INTO films (tytul, gatunek, rok_produkcji, ocena) VALUES ('$tytul', '$gatunek', $rok_produkcji, $ocena)";
        $db->query($add_query);
    }

    if (isset($_POST['modify'])) {
        $film_id = intval($_POST['film_id']);
    
        $fieldsToUpdate = [];
        if (!empty($_POST['tytul'])) {
            $tytul = $db->real_escape_string($_POST['tytul']);
            $fieldsToUpdate[] = "tytul = '$tytul'";
        }
        if (!empty($_POST['gatunek'])) {
            $gatunek = $db->real_escape_string($_POST['gatunek']);
            $fieldsToUpdate[] = "gatunek = '$gatunek'";
        }
        if (!empty($_POST['rok_produkcji'])) {
            $rok_produkcji = intval($_POST['rok_produkcji']);
            $fieldsToUpdate[] = "rok_produkcji = $rok_produkcji";
        }
        if ($_POST['ocena'] !== '') {
            $ocena = floatval($_POST['ocena']);
            $fieldsToUpdate[] = "ocena = $ocena";
        }
    
        if (!empty($fieldsToUpdate)) {
            $modify_query = "UPDATE films SET " . implode(', ', $fieldsToUpdate) . " WHERE film_id = $film_id";
            $db->query($modify_query);
        }
    }
    
    if (isset($_POST['delete'])) {
        $film_id = intval($_POST['film_id']);
        $delete_query = "DELETE FROM films WHERE film_id = $film_id";
        $db->query($delete_query);
    }

    if (isset($_POST['add_user'])) {
        $imie = $db->real_escape_string($_POST['imie']);
        $nazwisko = $db->real_escape_string($_POST['nazwisko']);
        $login = $db->real_escape_string($_POST['login']);
        $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
        $email = $db->real_escape_string($_POST['email']);
        $telefon = $db->real_escape_string($_POST['telefon']);
        $czyAdmin = $db->real_escape_string($_POST['czyAdmin']);
    
        $add_query = "INSERT INTO users (imie, nazwisko, login, haslo, email, telefon, czyAdmin) VALUES ('$imie', '$nazwisko', '$login', '$haslo', '$email', '$telefon', $czyAdmin)";
        $db->query($add_query);
    }
    
    if (isset($_POST['modify_user'])) {
        $login_to_modify = $db->real_escape_string($_POST['login_to_modify']);
    
        $fieldsToUpdate = [];
        if (!empty($_POST['new_imie'])) {
            $new_imie = $db->real_escape_string($_POST['new_imie']);
            $fieldsToUpdate[] = "imie = '$new_imie'";
        }
        if (!empty($_POST['new_nazwisko'])) {
            $new_nazwisko = $db->real_escape_string($_POST['new_nazwisko']);
            $fieldsToUpdate[] = "nazwisko = '$new_nazwisko'";
        }
        if (!empty($_POST['new_login'])) {
            $new_login = $db->real_escape_string($_POST['new_login']);
            $fieldsToUpdate[] = "login = '$new_login'";
        }
        if (!empty($_POST['new_haslo'])) {
            $new_haslo = password_hash($_POST['new_haslo'], PASSWORD_DEFAULT);
            $fieldsToUpdate[] = "haslo = '$new_haslo'";
        }
        if (!empty($_POST['new_email'])) {
            $new_email = $db->real_escape_string($_POST['new_email']);
            $fieldsToUpdate[] = "email = '$new_email'";
        }
        if (!empty($_POST['new_telefon'])) {
            $new_telefon = $db->real_escape_string($_POST['new_telefon']);
            $fieldsToUpdate[] = "telefon = '$new_telefon'";
        }
        if (isset($_POST['new_czyAdmin'])) {
            $new_czyAdmin = ($_POST['new_czyAdmin'] == '1') ? 1 : 0;
            $fieldsToUpdate[] = "czyAdmin = '$new_czyAdmin'";
        }
    
        if (!empty($fieldsToUpdate)) {
            $updateQuery = "UPDATE users SET " . implode(', ', $fieldsToUpdate) . " WHERE login = '$login_to_modify'";
            $db->query($updateQuery);
        }
    }
    
    
    if (isset($_POST['delete_user'])) {
        $login_to_delete = $db->real_escape_string($_POST['login_to_delete']);
    
        $delete_query = "DELETE FROM users WHERE login = '$login_to_delete'";
        $db->query($delete_query);
    }
    
}


$query = "SELECT * FROM films";
$result = $db->query($query);

echo "<h2>Films table:</h2>";

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

echo "<h3>Add record</h3>";
echo "<form method='post'>";
echo "<input type='text' name='tytul' placeholder='Title'>";
echo "<input type='text' name='gatunek' placeholder='Genre'>";
echo "<input type='number' name='rok_produkcji' placeholder='Year of Production'>";
echo "<input type='number' name='ocena' placeholder='Rating'>";
echo "<input type='submit' name='add' value='Add Record'>";
echo "</form>";

echo "<h3>Modify record</h3>";
echo "<form method='post'>";
echo "<input type='number' name='film_id' placeholder='Film ID'>";
echo "<input type='text' name='tytul' placeholder='New Title'>";
echo "<input type='text' name='gatunek' placeholder='New Genre'>";
echo "<input type='number' name='rok_produkcji' placeholder='New Year of Production'>";
echo "<input type='number' name='ocena' placeholder='New Rating'>";
echo "<input type='submit' name='modify' value='Modify Record'>";
echo "</form>";

echo "<h3>Delete record</h3>";
echo "<form method='post'>";
echo "<input type='number' name='film_id' placeholder='Film ID'>";
echo "<input type='submit' name='delete' value='Delete Record'>";
echo "</form>";


echo "<h2>Users table:</h2>";

echo "<h3>Add User</h3>";
echo "<form method='post'>";
echo "<input type='text' name='imie' placeholder='Name'>";
echo "<input type='text' name='nazwisko' placeholder='Surname'>";
echo "<input type='text' name='login' placeholder='Login'>";
echo "<input type='password' name='haslo' placeholder='Password'>";
echo "<input type='text' name='email' placeholder='Email'>";
echo "<input type='text' name='telefon' placeholder='Phone'>";
echo "<input type='text' name='czyAdmin' placeholder='IsAdmin {0 or 1}'>";
echo "<input type='submit' name='add_user' value='Add User'>";
echo "</form>";

echo "<h3>Modify User</h3>";
echo "<form method='post'>";
echo "<input type='text' name='login_to_modify' placeholder='Login of User to Modify'>";
echo "<input type='text' name='new_imie' placeholder='New Name'>";
echo "<input type='text' name='new_nazwisko' placeholder='New Surname'>";
echo "<input type='text' name='new_login' placeholder='New Login'>";
echo "<input type='password' name='new_haslo' placeholder='New Password'>";
echo "<input type='text' name='new_email' placeholder='New Email'>";
echo "<input type='text' name='new_telefon' placeholder='New Phone'>";
echo "<input type='text' name='czyAdmin' placeholder='IsAdmin {0 or 1}'>";
echo "<input type='submit' name='modify_user' value='Modify User'>";
echo "</form>";

echo "<h3>Delete User</h3>";
echo "<form method='post'>";
echo "<input type='text' name='login_to_delete' placeholder='Login of User to Delete'>";
echo "<input type='submit' name='delete_user' value='Delete User'>";
echo "</form>";

echo '<form method="post" action="">
            <input type="submit" name="logout" value="Logout">
          </form>';

?>
