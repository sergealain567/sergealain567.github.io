<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT pseudo, email, photo FROM utilisateurs ORDER BY pseudo ASC");
    $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des membres</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .membre {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .membre img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            border: 2px solid #3498db;
        }

        .membre-info {
            flex: 1;
        }

        .membre-info strong {
            display: block;
            font-size: 18px;
            color: #2c3e50;
        }

        .membre-info small {
            color: #666;
        }

        a.retour {
            display: inline-block;
            margin-top: 20px;
            background: #3498db;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 5px;
        }

        a.retour:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des membres inscrits</h1>

    <?php foreach ($membres as $membre): 
        $photo = (!empty($membre['photo']) && file_exists($membre['photo']))
            ? $membre['photo']
            : "https://cdn-icons-png.flaticon.com/512/147/147144.png";
    ?>
        <div class="membre">
            <img src="<?= htmlspecialchars($photo) ?>" alt="photo de <?= htmlspecialchars($membre['pseudo']) ?>">
            <div class="membre-info">
                <strong><?= htmlspecialchars($membre['pseudo']) ?></strong>
                <small><?= htmlspecialchars($membre['email']) ?></small>
            </div>
        </div>
    <?php endforeach; ?>

    <a class="retour" href="/projet_web/acceuil.php">← Retour</a>
</div>

</body>
</html>
