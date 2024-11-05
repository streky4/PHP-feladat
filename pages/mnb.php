<!DOCTYPE HTML>
<html lang="hu">
<head>
    <title>MNB Deviza Árfolyam</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        h1 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="date"] {
            padding: 10px;
            margin-bottom: 15px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #1b1f22; 
            color: white;
        }
        input[type="submit"] {
            padding: 10px 20px;
            margin-top: 10px;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .result {
            text-align: center;
            margin-top: 20px;
        }

        table {
            margin: 20px auto;
            border-collapse: collapse; /* Táblázat cellák közötti távolságok eltüntetése */
            width: 80%; /* Opció: 80%-ra méretezheted a táblázatot */
        }

        table th, table td {
            padding: 10px;
            text-align: center; /* A cellák szövege középre kerül */
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<h1>Deviza Árfolyam Lekérdezés</h1>

<form method="post">
    <label for="devizapara">Devizapár (pl. EUR,HUF /Fontos a megadott formátum, 3 nagy betű vesszővel elválasztva/):</label>
    <input type="text" name="devizapara" id="devizapara" required>
    <label for="datum">Dátum:</label>
    <input type="date" name="datum" id="datum" required>
    <input type="submit" name="lekérdez" value="Lekérdezés" class="primary">
</form>

<div class="result">
    <?php
    // PHP kód a SOAP híváshoz
    if (isset($_POST['lekérdez'])) {
        $devizapara = $_POST['devizapara'];
        $datum = $_POST['datum'];

        // A devizapár formátumának javítása: EUR-HUF -> EUR,HUF
        $devizapara = str_replace("-", ",", $devizapara);

        try {
            // SOAP kliens
            $client = new SoapClient("http://www.mnb.hu/arfolyamok.asmx?WSDL");

            // Lekérjük az árfolyamokat az adott napra
            $result = $client->GetExchangeRates([
                'startDate' => $datum,
                'endDate' => $datum,
                'currencyNames' => $devizapara
            ]);

            // A válasz feldolgozása
            $xml = simplexml_load_string($result->GetExchangeRatesResult);
            
            if (empty($xml)) {
                echo "<p>Nincs találat a megadott paraméterekkel.</p>";
            } else {
                echo "<h3>Árfolyamok {$devizapara} devizapárra a(z) {$datum} dátumra:</h3>";
                echo "<table><tr><th>Dátum</th><th>Árfolyam</th></tr>";
                foreach ($xml->Day as $day) {
                    foreach ($day->Rate as $rate) {
                        echo "<tr><td>" . htmlspecialchars($day['date']) . "</td><td>" . htmlspecialchars($rate) . "</td></tr>";
                    }
                }
                echo "</table>";
            }
        } catch (SoapFault $e) {
            echo "Hiba történt: " . $e->getMessage();
        }
    }
    ?>
</div>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>
