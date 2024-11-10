<!DOCTYPE HTML>
<html>
<head>
    <title>Restful API</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/main.css" />
</head>
<body>

<?php
include_once 'includes/db.php';
// Beállítjuk a kimeneti formátumot JSON-ra
header('Content-Type: application/json');

// Válasz üzenet változó
$response = array();

try {
    // Csatlakozás a MySQL adatbázishoz
    $dbh = new PDO('mysql:host=localhost;dbname=hangosfilmek', 'hangosfilmek', 'HuHJA9-1',
                  array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $dbh->query('SET NAMES utf8 COLLATE utf8_hungarian_ci');
    
    // API műveletek kezelése
    switch ($_SERVER['REQUEST_METHOD']) {
        case "GET":
            // Lekérdezzük az összes személyt
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
            // Új személy hozzáadása
            $incoming = file_get_contents("php://input");
            parse_str($incoming, $data);
            
            // SQL parancs a személy beszúrására
            $sql = "INSERT INTO szemely (nev, nem) VALUES (:nev, :nem)";
            $sth = $dbh->prepare($sql);
            $count = $sth->execute(Array(":nev" => $data["nev"], ":nem" => $data["nem"]));
            $newid = $dbh->lastInsertId();
            $response['success'] = true;
            $response['message'] = "$count beszúrt sor";
            $response['id'] = $newid;
            break;
        
        case "PUT":
            // Személy módosítása
            $data = array();
            $incoming = file_get_contents("php://input");
            parse_str($incoming, $data);
            
            $modositando = "id=id"; 
            $params = Array(":id" => $data["id"]);
            
            if ($data['nev'] != "") {
                $modositando .= ", nev = :nev"; 
                $params[":nev"] = $data["nev"];
            }
            if ($data['nem'] != "") {
                $modositando .= ", nem = :nem"; 
                $params[":nem"] = $data["nem"];
            }
            
            // SQL parancs a személy módosítására
            $sql = "UPDATE szemely SET " . $modositando . " WHERE id=:id";
            $sth = $dbh->prepare($sql);
            $count = $sth->execute($params);
            $response['success'] = true;
            $response['message'] = "$count módosított sor.";
            break;
        
        case "DELETE":
            // Személy törlése
            $data = array();
            $incoming = file_get_contents("php://input");
            parse_str($incoming, $data);
            
            // SQL parancs a személy törlésére
            $sql = "DELETE FROM szemely WHERE id=:id";
            $sth = $dbh->prepare($sql);
            $count = $sth->execute(Array(":id" => $data["id"]));
            $response['success'] = true;
            $response['message'] = "$count sor törölve.";
            break;
        
        default:
            // Ha nem megfelelő kérés, visszaadjuk a hibát
            $response['success'] = false;
            $response['message'] = "Nem támogatott kérési típus.";
            break;
    }
} catch (PDOException $e) {
    // Hiba kezelése
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// A válasz kimenete JSON formátumban
echo json_encode($response);
?>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>
