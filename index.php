<?php
session_start(); 
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Hangosfilmek</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Header -->
        <header id="header">
            <div class="logo">
                <span class="icon fa-gem"></span>
            </div>
            <div class="content">
                <div class="inner">
                    <h1>Hangosfilmek</h1>
                    <p>A két világháború között hazánkban virágzott a filmipar. A hangosfilm térhódításával egyre több, évente akár több tucat film is készült.<br>
                        Az adatbázis ezen filmek főbb adatait dolgozza fel ezen a reszponzív témájú, RESTFUL webszolgáltatáson keresztül.<br>
                    </p>
                </div>
            </div>
            <!-- Menük -->
            <nav>
                <ul>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li><a href="pages/mnb.php">MNB</a></li>
                        <li><a href="pages/pdf.php">PDF</a></li>
                        <li><a href="pages/soap.php">SOAP</a></li>
                        <li><a href="pages/restful.php">RESTFUL</a></li>
                    <?php else: ?>
                        <li><p style="color:red;">Csak bejelentkezett felhasználók férhetnek hozzá a menükhöz!</p></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <!-- Footer -->
        <footer id="footer">
            <div class="user-info">
                <?php if (isset($_SESSION['username'])): ?>
                    <p>Bejelentkezve mint: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p>Jogosultság: 
                        <?php
                        // Jogosultság megjelenítése
                        switch ($_SESSION['role']) {
                            case 0:
                                echo "Látogató";
                                break;
                            case 1:
                                echo "Regisztrált felhasználó";
                                break;
                            case 2:
                                echo "Adminisztrátor";
                                break;
                            default:
                                echo "Ismeretlen jogosultság";
                        }
                        ?>
                    </p>
                    <form action="logout.php" method="post">
                        <button type="submit">Kijelentkezés</button>
                    </form>
                <?php else: ?>
                    <p>Nincs bejelentkezve. <a href="pages/login.php">Bejelentkezés</a></p>
                <?php endif; ?>
            </div>
        </footer>

    </div>

    <!-- BG -->
    <div id="bg"></div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>