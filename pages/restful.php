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
        input, select { padding: 8px; margin: 10px 0; width: 200px; box-sizing: border-box; }
        h1, h2 { margin-top: 30px; }
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
        .back-button:hover { background-color: #28a745; }
        table { margin: 0 auto; border-collapse: collapse; width: 80%; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; }
    </style>
</head>
<body>
<a href="../index.php" class="back-button">Vissza a főoldalra</a>

<?php
$url = "http://hujbermate.nhely.hu/restfulServer.php";
$result = "";

// POST, PUT, DELETE - Adatok kezelése
if (isset($_POST['id'])) {
    $_POST['id'] = trim($_POST['id']);
    $_POST['nev'] = trim($_POST['nev']);
    $_POST['nem'] = trim($_POST['nem']);

    if ($_POST['id'] == "" && $_POST['nev'] != "" && $_POST['nem'] != "") {
        // POST kérés küldése JSON formátumban
        $data = array("nev" => $_POST["nev"], "nem" => $_POST["nem"]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  // JSON formátumban küldés
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));  // Fejléc beállítása JSON-ra
        $result = curl_exec($ch);
        curl_close($ch);
    } elseif ($_POST['id'] >= 1 && ($_POST['nev'] != "" || $_POST['nem'] != "")) {
        // PUT - Rekord módosítása
        $data = array("id" => $_POST["id"], "nev" => $_POST["nev"], "nem" => $_POST["nem"]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
    } elseif ($_POST['id'] >= 1 && $_POST['nev'] == "" && $_POST['nem'] == "") {
        // DELETE - Rekord törlése
        $data = array("id" => $_POST["id"]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
    } else {
        $result = "Hiba: Hiányos vagy érvénytelen adatok!";
    }
}

// GET - Rekordok lekérdezése
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$tabla = curl_exec($ch);
curl_close($ch);

$tabla_json = json_decode($tabla, true);
?>

<h1>Személyek listája:</h1>
<?php if (!empty($tabla_json['data'])): ?>
    <table>
        <tr>
            <th>Id</th>
            <th>Név</th>
            <th>Nem</th>
        </tr>
        <?php foreach ($tabla_json['data'] as $szemely): ?>
            <tr>
                <td><?= htmlspecialchars($szemely['id']) ?></td>
                <td><?= htmlspecialchars($szemely['nev']) ?></td>
                <td><?= htmlspecialchars($szemely['nem']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Nincs megjeleníthető adat.</p>
<?php endif; ?>

<h2>Adatok kezelése:</h2>
<form action="restful.php" method="post">
    <label>Azonosító (id):</label><br />
    <input type="text" name="id" /><br />
    <label>Név:</label><br />
    <input type="text" name="nev" /><br />
    <label>Nem:</label><br />
    <select name="nem">
        <option value=""></option>
        <option value="férfi">férfi</option>
        <option value="nő">nő</option>
    </select><br />
    <input type="submit" value="Küldés" />
</form>

<h2>Eredmény:</h2>
<?php
// Művelet eredménye
if (isset($result) && !empty($result)) {
    echo "Eredmény: " . htmlspecialchars($result);
} else {
    echo "Nincs visszaküldött eredmény.";
}
?>

</body>
</html>
