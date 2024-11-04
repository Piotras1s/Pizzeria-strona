<?php
session_start();

$host = "localhost";
$dbname = "pizzeria";
$user = "root";
$pass = "";

// Sprawdzenie, czy dane zostały przesłane metodą POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_zamowienia = $_POST['id_zamowienia'] ?? null;
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    if ($id_zamowienia !== null) {
        try {
            // Połączenie z bazą danych
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Aktualizacja statusu zamówienia
            $stmt = $pdo->prepare("UPDATE zamowienia SET is_completed = :is_completed WHERE id_zamowienia = :id_zamowienia");
            $stmt->bindParam(':is_completed', $is_completed, PDO::PARAM_INT);
            $stmt->bindParam(':id_zamowienia', $id_zamowienia, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Błąd: " . $e->getMessage();
        }
    }
}

// Po aktualizacji przekierowanie na stronę zamówień
header("Location: login.php");
exit;
?>
