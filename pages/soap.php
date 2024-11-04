<!DOCTYPE HTML>
<html>
<head>
    <title>SOAP</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px;
        }
        .button {
            padding: 10px 20px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<?php
// SOAP kliens beállítások
$options = array(
    "location" => "http://localhost/soapserver.php",
    "uri" => "http://localhost/soapserver.php",
    'keep_alive' => false,
);

try {
    $client = new SoapClient(null, $options);

    // Eredmény tárolására szolgáló változó
    $result = "";

    // Filmek listázása
    if (isset($_POST['listFilms'])) {
        $films = $client->__soapCall("getFilms", array());
        $result .= "<h3>Filmek:</h3><pre>" . print_r($films, true) . "</pre>";
    }

    // Film hozzáadása
    if (isset($_POST['addFilm'])) {
        $cim = $_POST['cim'];
        $gyartas = (int)$_POST['gyartas'];
        $hossz = (int)$_POST['hossz'];
        $bemutato = $_POST['bemutato'];
        $youtube = isset($_POST['youtube']) ? 1 : 0;
        $isAdded = $client->__soapCall("addFilm", array($cim, $gyartas, $hossz, $bemutato, $youtube));
        $result .= $isAdded ? "Film hozzáadva!" : "Hiba történt a film hozzáadása során.";
    }

    // Feladatok listázása
    if (isset($_POST['listTasks'])) {
        $tasks = $client->__soapCall("getTasks", array());
        $result .= "<h3>Feladatok:</h3><pre>" . print_r($tasks, true) . "</pre>";
    }

    // Személyek listázása
    if (isset($_POST['listPeople'])) {
        $people = $client->__soapCall("getPeople", array());
        $result .= "<h3>Személyek:</h3><pre>" . print_r($people, true) . "</pre>";
    }
    
} catch (SoapFault $e) {
    $result = "SOAP hiba: " . $e->getMessage();
}
?>

<div class="container">
    <h2>SOAP Kliens Menü</h2>

    <!-- Filmek listázása -->
    <form method="post">
        <input type="submit" name="listFilms" value="Filmek listázása" class="button primary">
    </form>

    <!-- Film hozzáadása -->
    <h3>Új film hozzáadása</h3>
    <form method="post">
        <div class="form-group">
            <label>Cím:</label>
            <input type="text" name="cim" required>
        </div>
        <div class="form-group">
            <label>Gyártási év:</label>
            <input type="number" name="gyartas" required>
        </div>
        <div class="form-group">
            <label>Hossz:</label>
            <input type="number" name="hossz" required>
        </div>
        <div class="form-group">
            <label>Bemutató dátuma:</label>
            <input type="date" name="bemutato" required>
        </div>
        <div class="form-group">
            <label>YouTube elérhetőség:</label>
            <input type="checkbox" name="youtube" value="1"> Elérhető
        </div>
        <input type="submit" name="addFilm" value="Film hozzáadása" class="button primary">
    </form>

    <!-- Feladatok listázása -->
    <form method="post">
        <input type="submit" name="listTasks" value="Feladatok listázása" class="button primary">
    </form>

    <!-- Személyek listázása -->
    <form method="post">
        <input type="submit" name="listPeople" value="Személyek listázása" class="button primary">
    </form>

    <!-- Eredmények kiírása -->
    <div>
        <?php if ($result) : ?>
            <h3>Eredmény:</h3>
            <div><?php echo $result; ?></div>
        <?php endif; ?>
    </div>
</div>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>
