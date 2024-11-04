<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";  // zmień na odpowiednie dane połączenia
$dbname = "pizzeria";
$user = "root";
$pass = "";  // hasło do bazy danych

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM zamowienia WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $zamowienia = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twoje Zamówienia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Twoje Zamówienia</h2>
    <?php if ($zamowienia): ?>
        <ul>
            <?php foreach ($zamowienia as $zamowienie): ?>
                <li><?php echo "Zamówienie nr: " . $zamowienie['id_zamowienia'] . " - " . $zamowienie['szczegoly']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Brak zamówień.</p>
    <?php endif; ?>
</body>
</html>
