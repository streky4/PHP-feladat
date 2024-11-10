<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>RESTful API Kliens</title>
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }

        form { display: inline-block; align-items: center; text-align: center; margin: 20px auto; }

        input, select {
            padding: 8px;
            margin: 10px 0;
            width: 200px;
            box-sizing: border-box;
        }

        h1, h2 {
            margin-top: 30px;
        }
        .back-button {
            display: block;
            width: max-content;
            padding: 10px 20px;
            margin: 20px auto;
            color: white;
            background-color: #1b1f22;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .back-button:hover {
            background-color: #28a745;
        }
    </style>
</head>
<body>
<a href="../index.php" class="back-button">Vissza a főoldalra</a>
<?php
$url = "http://localhost/restfulServerphp";
$result = "";

// Az adatok kezelése
if (isset($_POST['id'])) {
    $_POST['id'] = trim($_POST['id']);
    $_POST['nev'] = trim($_POST['nev']);
    $_POST['nem'] = trim($_POST['nem']);

    // POST - új rekord beszúrása
    if ($_POST['id'] == "" && $_POST['nev'] != "" && $_POST['nem'] != "") {
        $data = array("nev" => $_POST["nev"], "nem" => $_POST["nem"]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if(curl_errno($ch)) {
            $result = "Hiba: " . curl_error($ch);
        }

        curl_close($ch);
    }
    // PUT - rekord módosítása
    elseif ($_POST['id'] >= 1 && ($_POST['nev'] != "" || $_POST['nem'] != "")) {
        $data = array("id" => $_POST["id"], "nev" => $_POST["nev"], "nem" => $_POST["nem"]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if(curl_errno($ch)) {
            $result = "Hiba: " . curl_error($ch);
        }

        curl_close($ch);
    }
    // DELETE - rekord törlése
    elseif ($_POST['id'] >= 1 && $_POST['nev'] == "" && $_POST['nem'] == "") {
        $data = array("id" => $_POST["id"]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if(curl_errno($ch)) {
            $result = "Hiba: " . curl_error($ch);
        }

        curl_close($ch);
    }
    // Hiányzó adatok esetén hibaüzenet
    else {
        $result = "Hiba: Hiányos vagy érvénytelen adatok!";
    }
}

// GET - rekordok lekérdezése
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
$tabla = curl_exec($ch);

if(curl_errno($ch)) {
    $tabla = "Hiba: " . curl_error($ch);
} elseif ($tabla === false || empty($tabla)) {
    $tabla = "Nincs megjeleníthető adat.";
}

curl_close($ch);

// Ha JSON választ kaptunk, dekódoljuk
$tabla_json = json_decode($tabla, true);
if ($tabla_json !== null) {
    $tabla = "<pre>" . print_r($tabla_json, true) . "</pre>";
}
?>

<p><?= htmlspecialchars($result) ?></p>
<h1>Személyek listája:</h1>
<div><?= $tabla ?></div>

<h2>Beszúrás / Módosítás / Törlés</h2>
<form method="post">
    Id: <input type="text" name="id" placeholder="csak módosításhoz/törléshez"><br><br>
    Név: <input type="text" name="nev"><br><br>
    Nem: 
    <select name="nem">
        <option value="férfi">férfi</option>
        <option value="nő">nő</option>
    </select><br><br>
    <input type="submit" value="Küldés">
</form>

</body>
</html>
