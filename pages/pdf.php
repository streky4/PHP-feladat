<?php
ob_start(); // Az oldal elején kimenet elnyomása

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // TCPDF betöltése
    require_once('../TCPDF/tcpdf.php');
    
    include '../includes/db.php';

    // Felhasználói bemenetek lekérése
    $gyartas = $_POST['gyartas'];
    $nem = $_POST['nem'];
    $feladat = $_POST['feladat'];

    // Adatok lekérdezése
    $sql = "SELECT f.cim, f.gyartas, f.hossz, f.bemutato, s.nev
            FROM feladat AS fl
            INNER JOIN film AS f ON fl.filmid = f.id
            INNER JOIN szemely AS s ON fl.szemelyid = s.id
            WHERE f.gyartas = ? AND s.nem = ? AND fl.megnevezes = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $gyartas, $nem, $feladat);
    $stmt->execute();
    $result = $stmt->get_result();

    // PDF beállítások és készítés
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('PDF Generátor');
    $pdf->SetTitle('Hangosfilmek jelentés');
    $pdf->SetHeaderData('', '', 'Hangosfilmek', "Gyártási év: $gyartas | Nem: $nem | Feladat: $feladat");
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 12);

    // PDF tartalom generálása
    ob_end_clean(); // Kimenet törlése a PDF generálása előtt

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="hangosfilmek_jelentes.pdf"');
    
    $html = '<h2>Hangosfilmek jelentés</h2>';
    $html .= '<table border="1" cellpadding="5">';
    $html .= '<tr><th>Film Cím</th><th>Gyártási év</th><th>Hossz</th><th>Bemutató</th><th>Személy neve</th></tr>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                        <td>' . $row['cim'] . '</td>
                        <td>' . $row['gyartas'] . '</td>
                        <td>' . $row['hossz'] . ' perc</td>
                        <td>' . $row['bemutato'] . '</td>
                        <td>' . $row['nev'] . '</td>
                      </tr>';
        }
    } else {
        $html .= '<tr><td colspan="5">Nincs találat.</td></tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('hangosfilmek_jelentes.pdf', 'I');

    // Kapcsolat lezárása
    $conn->close();
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>PDF Generátor</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/main.css" />

    <style>
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
        h2 {
            text-align: center;
        }
        form {
            max-width: 400px;
            margin: auto;
            text-align: center;
        }
        select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<a href="../index.php" class="back-button">Vissza a főoldalra</a>

<h2>PDF Generálás</h2>
<form action="" method="post"> 
    <label for="gyartasi_ev">Gyártási év:</label>
    <select name="gyartas" id="gyartas" required>
        <?php
        for ($year = 1900; $year <= 2000; $year++) {
            echo "<option value=\"$year\">$year</option>";
        }
        ?>
    </select>
    
    <label for="nem">Nem:</label>
    <select name="nem" id="nem" required>
        <option value="férfi">Férfi</option>
        <option value="nő">Nő</option>
    </select>
    
    <label for="feladat">Feladat:</label>
    <select name="feladat" id="feladat" required>
        <option value="forgatókönyvíró">Forgatókönyvíró</option>
        <option value="operatőr">Operatőr</option>
        <option value="rendező">Rendező</option>
        <option value="színész">Színész</option>
    </select>
    
    <input type="submit" value="PDF Generálás">
</form>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>
