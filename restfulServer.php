<?php
include_once '../includes/db.php';

// CORS beállítások
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');  
header('Access-Control-Allow-Headers: Content-Type');  

// Ha az előkészítő kérés (OPTIONS), akkor csak az engedélyek visszaküldése
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$response = array();

try {
    // Csatlakozás a MySQL-hez
    $dbh = new PDO('mysql:host=localhost;dbname=hangosfilmek', 'hangosfilmek', 'HuHJA9-1', 
                   array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    
    // API műveletek
    switch ($_SERVER['REQUEST_METHOD']) {
        case "GET":
            $sql = "SELECT * FROM szemely";     
            $sth = $dbh->query($sql);
            $result = array();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }
            $response['data'] = $result;
            $response['success'] = true;
            break;

        case "POST":
            $incoming = file_get_contents("php://input");
            $data = json_decode($incoming, true); 
            
            // Adatok megjöttek?
            if (isset($data["nev"]) && isset($data["nem"])) {
                $sql = "INSERT INTO szemely (nev, nem) VALUES (:nev, :nem)";
                $sth = $dbh->prepare($sql);
                $count = $sth->execute(Array(":nev" => $data["nev"], ":nem" => $data["nem"]));
                $newid = $dbh->lastInsertId();
        
                $response['success'] = true;
                $response['message'] = "$count beszúrt sor";
                $response['id'] = $newid;
            } else {
                $response['success'] = false;
                $response['message'] = "Hiányzó adatok";
            }
            break;

        case "PUT":
            $incoming = file_get_contents("php://input");
            parse_str($incoming, $data);

            $modositando = "id=id"; 
            $params = Array(":id" => $data["id"]);
            
            if (!empty($data['nev'])) {
                $modositando .= ", nev = :nev"; 
                $params[":nev"] = $data["nev"];
            }
            if (!empty($data['nem'])) {
                $modositando .= ", nem = :nem"; 
                $params[":nem"] = $data["nem"];
            }

            $sql = "UPDATE szemely SET " . $modositando . " WHERE id=:id";
            $sth = $dbh->prepare($sql);
            $count = $sth->execute($params);

            $response['success'] = true;
            $response['message'] = "$count módosított sor.";
            break;

        case "DELETE":
            $incoming = file_get_contents("php://input");
            parse_str($incoming, $data);

            $sql = "DELETE FROM szemely WHERE id=:id";
            $sth = $dbh->prepare($sql);
            $count = $sth->execute(Array(":id" => $data["id"]));

            $response['success'] = true;
            $response['message'] = "$count sor törölve.";
            break;

        default:
            http_response_code(405);  // Method Not Allowed
            $response['success'] = false;
            $response['message'] = "Nem támogatott kérési típus.";
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);  // Internal Server Error
    $response['success'] = false;
    $response['message'] = "Adatbázis hiba: " . $e->getMessage();
} catch (Exception $e) {
    http_response_code(500);  // Internal Server Error
    $response['success'] = false;
    $response['message'] = "Hiba történt: " . $e->getMessage();
}

// Csak JSON válasz küldése, HTML nélkül
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
