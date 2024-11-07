<!DOCTYPE HTML>
<html lang="hu">
<head>
    <title>MNB Deviza Árfolyam</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        h1 { text-align: center; }
        form { display: flex; flex-direction: column; align-items: center; text-align: center; margin: 20px auto; }
        label { margin: 10px 0 5px; }
        input[type="text"], input[type="date"], select {
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
        input[type="submit"]:hover { background-color: #0056b3; }
        .result { text-align: center; margin-top: 20px; }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
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

<h1>Deviza Árfolyam Lekérdezés</h1>



<!-- Napi deviza lekérdezés űrlap -->
<form method="post">
    <label for="devizapara">Devizapár (Fontos a megadott formátum, 3 nagy betű vesszővel elválasztva, szóköz nélkül!):</label>
    <input type="text" name="devizapara" id="devizapara" required placeholder="pl. EUR,HUF">
    <label for="datum">Dátum:</label>
    <input type="date" name="datum" id="datum" required>
    <input type="submit" name="lekérdez" value="Lekérdezés" class="primary">
    <br>
</form>

<!-- Év és hónap választó űrlap -->
<h1>Visszamenőleges árfolyam lekérdezés</h1>
<form method="post">
    <label for="devizapara_honap">Devizapár:</label>
    <input type="text" name="devizapara_honap" id="devizapara_honap" required placeholder="pl. EUR,HUF">
    
    <label for="year">Év:</label>
    <select name="year" id="year" required>
        <?php
        $currentYear = date("Y");
        for ($i = $currentYear; $i >= 2000; $i--) {
            echo "<option value=\"$i\">$i</option>";
        }
        ?>
    </select>

    <label for="month">Hónap:</label>
    <select name="month" id="month" required>
        <?php
        $months = [
            1 => "Január", 2 => "Február", 3 => "Március", 4 => "Április", 5 => "Május", 6 => "Június",
            7 => "Július", 8 => "Augusztus", 9 => "Szeptember", 10 => "Október", 11 => "November", 12 => "December"
        ];

        foreach ($months as $index => $month) {
            echo "<option value=\"$index\">$month</option>";
        }
        ?>
    </select>

    <input type="submit" name="lekérdez_honap" value="Lekérdezés" class="primary">
</form>

<div class="result">
    <?php
    if (isset($_POST['lekérdez'])) {
        // Napi lekérdezés
        $devizapara = $_POST['devizapara'];
        $datum = $_POST['datum'];

        $client = new SoapClient("http://www.mnb.hu/arfolyamok.asmx?WSDL");

        // Megadott devizapárok ellenőrzése
        $devizak = explode(",", $devizapara);

        // Árfolyam lekérdezés
        if ($devizak[0] == 'HUF') {
            $result1 = $client->GetExchangeRates([ 
                'startDate' => $datum,
                'endDate' => $datum,
                'currencyNames' => $devizak[1]
            ]);
        } else {
            $result1 = $client->GetExchangeRates([ 
                'startDate' => $datum,
                'endDate' => $datum,
                'currencyNames' => $devizak[0]
            ]);
        }

        $xml1 = simplexml_load_string($result1->GetExchangeRatesResult);
        $rate1 = isset($xml1->Day->Rate) ? (float)$xml1->Day->Rate : 0;

        // Ha van eredmény, megjelenítjük a táblázatot és a grafikonhoz szükséges adatokat
        if ($rate1 > 0) {
            echo "<h3>Árfolyam a(z) {$datum} dátumra:</h3>";
            echo "<table><tr><th>Devizapár</th><th>Árfolyam</th></tr>";
            echo "<tr><td>{$devizak[0]}/{$devizak[1]}</td><td>" . number_format($rate1, 4) . "</td></tr>";
            echo "</table>";

            // Javascript grafikonhoz szükséges adat
            $jsData = json_encode([
                "labels" => [$datum], 
                "data" => [$rate1]
            ]);
        } else {
            echo "<p>Az egyik vagy mindkét deviza árfolyama nem érhető el a megadott dátumra.</p>";
            $jsData = json_encode([
                "labels" => [],
                "data" => []
            ]);
        }
    }

    if (isset($_POST['lekérdez_honap'])) {
        // Hónap alapján lekérdezés
        $devizapara_honap = $_POST['devizapara_honap'];
        $devizak_honap = explode(",", $devizapara_honap);
        $year = $_POST['year'];
        $month = str_pad($_POST['month'], 2, '0', STR_PAD_LEFT);
        $startDate = "{$year}-{$month}-01";
        $endDate = date("Y-m-t", strtotime($startDate));

        $client = new SoapClient("http://www.mnb.hu/arfolyamok.asmx?WSDL");

        
        $result = $client->GetExchangeRates([ 
            'startDate' => $startDate,
            'endDate' => $endDate,
            'currencyNames' => $devizak_honap[1]
        ]);

        // Ha van válasz, megjelenítjük az adatokat
        if (isset($result->GetExchangeRatesResult)) {
            $xml = simplexml_load_string($result->GetExchangeRatesResult);
            if (isset($xml->Day)) {
                echo "<h3>{$year}.{$month} havi árfolyamok ({$devizak_honap[0]}/{$devizak_honap[1]}):</h3>";
                echo "<table><tr><th>Dátum</th><th>Devizapár</th><th>Árfolyam</th></tr>";

                // Grafikonhoz adatok
                $labels = [];
                $data = [];
                foreach ($xml->Day as $day) {
                    $date = (string)$day->attributes()->date;
                    $rate = (string)$day->Rate;
                    $formattedRate = number_format(str_replace(",", ".", $rate), 2, ".", "");

                    echo "<tr><td>{$date}</td><td>{$devizak_honap[0]}/{$devizak_honap[1]}</td><td>{$formattedRate}</td></tr>";
                    $labels[] = $date;
                    $data[] = $formattedRate;
                }

                echo "</table>";
                $jsData = json_encode([
                    "labels" => $labels,
                    "data" => $data
                ]);
            } else {
                echo "<p>Nincs válasz az MNB-től a megadott hónapra.</p>";
                $jsData = json_encode([
                    "labels" => [],
                    "data" => []
                ]);
            }
        } else {
            echo "<p>Nincs válasz az MNB-től, kérlek próbáld újra később.</p>";
            $jsData = json_encode([
                "labels" => [],
                "data" => []
            ]);
        }
    }
    ?>
</div>

<!-- Grafikon megjelenítése -->
<div>
  <canvas id="myChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('myChart');
  const chartData = <?php echo $jsData; ?>; // A PHP-ből származó grafikon adatok

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: chartData.labels,
      datasets: [{
        label: 'Deviza Árfolyam',
        data: chartData.data,
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        fill: false
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: false
        }
      }
    }
  });
</script>
</body>
</html>
