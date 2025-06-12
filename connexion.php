<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['valider'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($password === $user['password']) {
            $_SESSION['user'] = $user['pseudo'];
            $_SESSION['email'] = $user['email'];
            header("Location: /projet_web/acceuil.php");
            exit();
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Email non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .menu-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
            font-size: 26px;
        }

        .nav-menu {
            position: absolute;
            top: 60px;
            right: 20px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: none;
            flex-direction: column;
            min-width: 150px;
        }

        .nav-menu a {
            padding: 10px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #eee;
        }

        .nav-menu a:last-child {
            border-bottom: none;
        }

        .nav-menu a:hover {
            background-color: #f0f0f0;
        }

        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 360px;
            margin: 100px auto;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: red;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Menu icône et liens -->
<div class="menu-icon" onclick="toggleMenu()">Menu ☰</div>
<div class="nav-menu" id="navMenu">
    <a href="connexion.php">🔑 Connexion</a>
    <a href="/projet_web/inscription.html">📝 Inscription</a>
</div>

<!-- Formulaire de connexion -->
<div class="login-container">
    <h2>Connexion</h2>

    <?php if (!empty($message)) echo "<p class='error-message'>$message</p>"; ?>

    <form method="POST" action="connexion.php" autocomplete="off">
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" autocomplete=new-password required>

        <button type="submit" name="valider">Se connecter</button>
    </form>
</div>

<!-- Script pour le menu -->
<script>
function toggleMenu() {
    const menu = document.getElementById('navMenu');
    menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
}
</script>

</body>
</html>
