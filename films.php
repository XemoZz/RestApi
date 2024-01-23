<?php
header('Content-Type: application/json');

$db = new mysqli('127.0.0.1', 'root', '', 'rest_api');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $result = $db->query("SELECT * FROM films");
        $films = [];
        while ($row = $result->fetch_assoc()) {
            $films[] = $row;
        }
        echo json_encode($films);
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $tytul = $db->real_escape_string($data['tytul']);
            $gatunek = $db->real_escape_string($data['gatunek']);
            $rok_produkcji = intval($data['rok_produkcji']);
            $ocena = floatval($data['ocena']);

            $add_query = "INSERT INTO films (tytul, gatunek, rok_produkcji, ocena) VALUES ('$tytul', '$gatunek', $rok_produkcji, $ocena)";
            if ($db->query($add_query)) {
                echo json_encode(["message" => "Film dodany pomyślnie"]);
            } else {
                echo json_encode(["message" => "Błąd podczas dodawania filmu"]);
            }
        } else {
            echo json_encode(["message" => "Nieprawidłowe dane"]);
        }
        break;

    case 'PUT':
        
        
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriSegments = explode('/', $uri);

        $film_id = isset($uriSegments[3]) ? intval($uriSegments[3]) : null;

        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($film_id && $data) {
            $fieldsToUpdate = [];

            foreach ($data as $key => $value) {
                $value = $db->real_escape_string($value);
                switch ($key) {
                    case 'tytul':
                        $fieldsToUpdate[] = "tytul = '$value'";
                        break;
                    case 'gatunek':
                        $fieldsToUpdate[] = "gatunek = '$value'";
                        break;
                    case 'rok_produkcji':
                        $value = intval($value);
                        $fieldsToUpdate[] = "rok_produkcji = '$value'";
                        break;
                    case 'ocena':
                        $value = floatval($value);
                        $fieldsToUpdate[] = "ocena = '$value'";
                        break;
                }
            }

            if (!empty($fieldsToUpdate)) {
                $updateQuery = "UPDATE films SET " . implode(', ', $fieldsToUpdate) . " WHERE film_id = $film_id";
                if ($db->query($updateQuery)) {
                    echo json_encode(["message" => "Film zaktualizowany pomyślnie"]);
                } else {
                    echo json_encode(["message" => "Błąd podczas aktualizacji filmu"]);
                }
            } else {
                echo json_encode(["message" => "Brak danych do aktualizacji"]);
            }
        } else {
            echo json_encode(["message" => "Nieprawidłowe ID filmu lub dane"]);
        }
        break;


    case 'DELETE':
 
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriSegments = explode('/', $uri);

        $film_id = isset($uriSegments[3]) ? intval($uriSegments[3]) : null;

        if ($film_id) {
            $delete_query = "DELETE FROM films WHERE film_id = $film_id";
            if ($db->query($delete_query)) {
                echo json_encode(["message" => "Film usunięty pomyślnie"]);
            } else {
                echo json_encode(["message" => "Błąd podczas usuwania filmu"]);
            }
        } else {
            echo json_encode(["message" => "Nieprawidłowe ID filmu"]);
        }
        break;


    default:
        echo json_encode(["message" => "Metoda HTTP nieobsługiwana"]);
        break;
}

$db->close();
?>
