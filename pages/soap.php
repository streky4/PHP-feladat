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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #fff; 
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px;
            background-color: #1b1f22; 
            border: 1px solid #ddd;
            color: #fff; 
            border-radius: 4px;
        }
        .form-group input[type="checkbox"] {
            margin-right: 10px; 
            accent-color: #fff; 
        }
        .button {
            padding: 10px 20px;
            margin-top: 10px;
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
// SOAP kliens beállítások
$options = array(
    "location" => "http://hujbermate.nhely.hu/soapserver.php",
    "uri" => "http://hujbermate.nhely.hu/soapserver.php",
    'keep_alive' => false,
);

try {
    $client = new SoapClient(null, $options);

    // Eredmény tárolásáa
    $result = "";

    // Filmek listázása
if (isset($_POST['listFilms'])) {
    $films = $client->__soapCall("getFilms", array());

    // Táblázat generálása
    $result .= "<h3>Filmek:</h3>";
    $result .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%; text-align: left;'>";
    $result .= "<tr><th>ID</th><th>Cím</th><th>Gyártási év</th><th>Hossz (perc)</th><th>Bemutató dátuma</th><th>YouTube elérhetőség</th></tr>";

    foreach ($films as $film) {
        $result .= "<tr>";
        $result .= "<td>" . htmlspecialchars($film['id']) . "</td>";
        $result .= "<td>" . htmlspecialchars($film['cim']) . "</td>";
        $result .= "<td>" . htmlspecialchars($film['gyartas']) . "</td>";
        $result .= "<td>" . htmlspecialchars($film['hossz']) . "</td>";
        $result .= "<td>" . htmlspecialchars($film['bemutato']) . "</td>";
        $result .= "<td>" . ($film['youtube'] ? "Elérhető" : "Nem elérhető") . "</td>";
        $result .= "</tr>";
    }

    $result .= "</table>";
}


    // Film hozzáadása
    if (isset($_POST['addFilm'])) {
        $cim = $_POST['cim'];
        $gyartas = (int)$_POST['gyartas'];
        $hossz = (int)$_POST['hossz'];
        $bemutato = $_POST['bemutato'];
        $youtube = (int)$_POST['youtube']; // 1, ha "Elérhető", 0 ha "Nem elérhető"
        $isAdded = $client->__soapCall("addFilm", array($cim, $gyartas, $hossz, $bemutato, $youtube));
        $result .= $isAdded ? "Film hozzáadva!" : "Hiba történt a film hozzáadása során.";
    }

    // Feladatok listázása
if (isset($_POST['listTasks'])) {
    $tasks = $client->__soapCall("getTasks", array());

    // Táblázat generálása az eredményekből
    $result .= "<h3>Feladatok:</h3>";
    $result .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%; text-align: left;'>";
    $result .= "<tr><th>ID</th><th>Film ID</th><th>Személy ID</th><th>Megnevezés</th></tr>";

    foreach ($tasks as $task) {
        $result .= "<tr>";
        $result .= "<td>" . htmlspecialchars($task['id']) . "</td>";
        $result .= "<td>" . htmlspecialchars($task['filmid']) . "</td>";
        $result .= "<td>" . htmlspecialchars($task['szemelyid']) . "</td>";
        $result .= "<td>" . htmlspecialchars($task['megnevezes']) . "</td>";
        $result .= "</tr>";
    }

    $result .= "</table>";
}


    // Személyek listázása
if (isset($_POST['listPeople'])) {
    $people = $client->__soapCall("getPeople", array());

    // Táblázat generálása az eredményekből
    $result .= "<h3>Személyek:</h3>";
    $result .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%; text-align: left;'>";
    $result .= "<tr><th>ID</th><th>Név</th><th>Nem</th></tr>";

    foreach ($people as $person) {
        $result .= "<tr>";
        $result .= "<td>" . htmlspecialchars($person['id']) . "</td>";
        $result .= "<td>" . htmlspecialchars($person['nev']) . "</td>";
        $result .= "<td>" . htmlspecialchars($person['nem']) . "</td>";
        $result .= "</tr>";
    }

    $result .= "</table>";
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
        <label>YouTube elérhetőség:</label><br>
        <input type="radio" name="youtube" value="1" id="youtubeYes" required>
        <label for="youtubeYes">Elérhető</label><br>
        <input type="radio" name="youtube" value="0" id="youtubeNo" required>
        <label for="youtubeNo">Nem elérhető</label>
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
