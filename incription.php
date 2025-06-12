<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion
$host = 'localhost';
$dbname = 'projet';
$username = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

// Traitement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['valider'])) {
    $pseudo = htmlspecialchars(trim($_POST['pseudo']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $sexe = $_POST['sexe'];
   // $password = password_hash($password, PASSWORD_DEFAULT);
    $today = date("Y-m-d");
    $hour = date("H:i:s"); 

    // Vérifie si l'email est déjà utilisé
    $check = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
    $check->execute([$email]);

    if ($check->fetchColumn() > 0) {
        echo "Cet email est déjà utilisé. <a href='formulaire.html'>Retour</a>";
        exit();
    } else {
        $sql = 'INSERT INTO utilisateurs (pseudo, email, password, date, sexe, heure) 
                VALUES (:pseudo, :email, :password, :date, :sexe ,:heure)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':date', $today);
        $stmt->bindParam(':heure', $hour);
        $stmt->bindParam(':sexe', $sexe);

        try {
            $stmt->execute();
            header("Location: //localhost/projet_web/connexion.php"); // redirige vers connexion
            exit();
        } catch (PDOException $e) {
            echo "Erreur d'inscription : " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
