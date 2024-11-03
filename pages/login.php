<?php
session_start(); 
include '../includes/db.php'; 

// Bejelentkezési logika
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Beérkező adatok validálása
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Ellenőrizzük, hogy a felhasználó létezik-e az adatbázisban
    $stmt = $conn->prepare("SELECT id, role, password FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();

    // Ha a felhasználó létezik
    if ($stmt->num_rows > 0) {
        
        $stmt->bind_result($id, $role, $hashed_password);
        $stmt->fetch();

        // Jelszó ellenőrzése
        if (password_verify($password, $hashed_password)) {
            
            $_SESSION['username'] = $name;
            $_SESSION['role'] = $role;

          
            header("Location: ../index.php");
            exit();
        } else {
            // Hibaüzenet ha a bejelentkezés sikertelen
            echo "Hibás név vagy jelszó!";
        }
    } else {
        // Hibaüzenet ha a bejelentkezés sikertelen
        echo "Hibás név vagy jelszó!";
    }
}
?>


<!DOCTYPE HTML>
<html>
<head>
    <title>Bejelentkezés</title>
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
        <h2 class="major">Bejelentkezés</h2>
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
                <li><a href="regi.php" class="button">Regisztráció</a></li>
            </ul>
        </form>
    </div>
</div>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>
