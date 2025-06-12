<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$_SESSION['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: connexion.php");
        exit();
    }

    $message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = $_FILES['photo']['name'];
        $fileTmp = $_FILES['photo']['tmp_name'];
        $fileError = $_FILES['photo']['error'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileError === 0) {
            if (in_array($fileExt, $allowed)) {
                $newFileName = uniqid('avatar_', true) . '.' . $fileExt;
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmp, $destination)) {
                    $stmtUpdate = $pdo->prepare("UPDATE utilisateurs SET photo = ? WHERE email = ?");
                    if ($stmtUpdate->execute([$destination, $user['email']])) {
                        $message = "✅ Photo mise à jour avec succès !";
                        $user['photo'] = $destination;
                    } else {
                        $message = "❌ Erreur lors de la mise à jour dans la base.";
                    }
                } else {
                    $message = "❌ Échec de l'enregistrement du fichier.";
                }
            } else {
                $message = "❌ Format non autorisé. (jpg, jpeg, png, gif)";
            }
        } else {
            $message = "❌ Erreur d'upload (code $fileError).";
        }
    }

    $photoProfil = (!empty($user['photo']) && file_exists($user['photo']))
        ? $user['photo']
        : "https://cdn-icons-png.flaticon.com/512/147/147144.png";

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil utilisateur</title>
    <style>




        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 50px;
            text-align: center;
        }

        .profil {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            max-width: 420px;
            margin: auto;
        }

        h1 {
            margin: 10px 0 5px;
            color: #2c3e50;
        }

        h2 {
            color: #3498db;
            margin-bottom: 10px;
        }

        .profil img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 10px;
            border: 3px solid #3498db;
            cursor: pointer;
            transition: 0.3s;
        }

        .profil img:hover {
            opacity: 0.8;
        }

        .info {
            margin-top: 15px;
            font-size: 16px;
            color: #333;
        }

        a.logout-btn {
            display: inline-block;
            margin-top: 20px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
        }

        a.logout-btn:hover {
            background: #c0392b;
        }

        form {
            margin-top: 20px;
        }

        input[type="file"] {
            display: none;
        }

        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #2980b9;
        }

        .message {
            margin-top: 15px;
            color: green;
            font-weight: bold;
        }

        a.members-btn {
    display: inline-block;
    margin-top: 10px;
    background: #2ecc71;
    color: white;
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-weight: bold;
}

a.members-btn:hover {
    background: #27ae60;
}


    </style>
</head>
<body>



<div class="profil">
    <h2>Photo de profil</h2>

    <form method="POST" enctype="multipart/form-data" id="photoForm">
        <label for="photo">
            <img src="<?= htmlspecialchars($photoProfil) ?>" alt="Photo de profil" title="Cliquez pour changer la photo">
        </label>
        <input type="file" name="photo" id="photo" accept="image/*" required><br><br>
        <button type="submit">Mettre à jour</button>
    </form>

    <h1>Bienvenue, <?= htmlspecialchars($user['pseudo']) ?> !</h1>
    <p class="info">Email : <strong><?= htmlspecialchars($user['email']) ?></strong></p>

    <a class="logout-btn" href="logout.php">Se déconnecter</a> <br><br>

    
    <a class="members-btn" href="membres.php">👥 Voir les membres</a>
    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
</div>

<script>
    // Aperçu immédiat de la photo sélectionnée
    document.getElementById('photo').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const img = document.querySelector('label[for="photo"] img');
            img.src = URL.createObjectURL(file);
        }
    });
</script>

</body>
</html>
