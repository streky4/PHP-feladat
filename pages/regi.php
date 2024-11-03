<!DOCTYPE HTML>
<html>
<head>
    <title>Regisztráció</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh; 
        }
        .login-box {
            padding: 2em;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 1em;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2 class="major">Regisztráció</h2>
        <?php
        // Az adatbázis kapcsolat betöltése
        include '../includes/db.php'; 

        // űrlap elküldésének ellenőrzése
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST['name'];
            $password = $_POST['password'];

            // Ellenőrizni, hogy a felhasználónév már létezik-e
            $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<p style='color:red;'>A felhasználónév már foglalt!</p>";
            } else {
                // Jelszó hash-elése
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Felhasználó hozzáadása az adatbázishoz
                $stmt = $conn->prepare("INSERT INTO users (name, password, role) VALUES (?, ?, ?)");
                $role = 1; // új felhasználó jogosultsági értéke
                $stmt->bind_param("ssi", $name, $hashedPassword, $role);

                if ($stmt->execute()) {
                    // Redirect az index.php oldalra
                    header("Location: ../index.php");
                    exit(); 
                } else {
                    echo "<p style='color:red;'>Hiba a regisztrálás során!</p>";
                }
            }

            // Kapcsolat lezárása
            $stmt->close();
        }
        ?>
        <form method="post" action="">
            <div class="fields">
                <div class="field">
                    <label for="name">Név</label>
                    <input type="text" name="name" id="name" required />
                </div>
                <div class="field">
                    <label for="password">Jelszó</label>
                    <input type="password" name="password" id="password" required />
                </div>
            </div>
            <ul class="actions">
                <li><input type="submit" value="Rendben" class="primary" /></li>
                <li><input type="reset" value="Reset" /></li>
            </ul>
        </form>
    </div>
</div>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>
