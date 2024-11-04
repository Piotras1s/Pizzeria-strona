<?php
session_start();

// Konfiguracja połączenia z bazą danych
$host = "localhost";
$dbname = "pizzeria";
$user = "root";
$pass = "";

// Inicjalizacja zmiennych
$zamowienia = [];
$loginError = "";
$pracownicy = [];

try {
    // Połączenie z bazą danych
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sprawdzenie, czy formularz został wysłany
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Logowanie użytkownika
        if (isset($_POST['login'])) {
            $login = $_POST['login'] ?? '';
            $haslo = $_POST['haslo'] ?? '';

            $stmt = $pdo->prepare("SELECT * FROM logowanie WHERE login = :login");
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $user['haslo'] === $haslo) {
                $_SESSION['user_id'] = $user['id'];

                // Pobranie zamówień
                $stmt = $pdo->prepare("SELECT z.id_zamowienia, z.imie, z.nazwisko, z.nr_telefonu, z.adres_zamieszkania, z.metoda_dostawy, z.total_price, z.created_at, z.is_completed, zp.id_pracownik 
                                       FROM zamowienia z 
                                       LEFT JOIN zamowienia_pracownik zp ON z.id_zamowienia = zp.id_zamowienia 
                                       ORDER BY z.created_at DESC");
                $stmt->execute();
                $zamowienia = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Pobranie pracowników
                $stmt = $pdo->prepare("SELECT id_pracownik, pracownik FROM pracownicy");
                $stmt->execute();
                $pracownicy = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $loginError = "Nieprawidłowy login lub hasło.";
            }
        } 
        // Aktualizacja statusu zamówienia i przypisanie pracownika
        else if (isset($_POST['update_status'])) {
            foreach ($_POST['status'] as $orderId => $status) {
                $stmtUpdate = $pdo->prepare("UPDATE zamowienia SET is_completed = :status WHERE id_zamowienia = :order_id");
                $stmtUpdate->bindParam(':status', $status, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                $stmtUpdate->execute();
                
                // Zapisanie przypisania pracownika do zamówienia
                $pracownikId = $_POST['pracownik'][$orderId];
                $stmtAssignWorker = $pdo->prepare("INSERT INTO zamowienia_pracownik (id_zamowienia, id_pracownik) VALUES (:order_id, :pracownik_id) ON DUPLICATE KEY UPDATE id_pracownik = :pracownik_id");
                $stmtAssignWorker->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                $stmtAssignWorker->bindParam(':pracownik_id', $pracownikId, PDO::PARAM_INT);
                $stmtAssignWorker->execute();
            }
        }
    }
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet">
    <style>
        /* Stylizacja */
        .login-container {
            width: 100%;
            margin: auto;
            padding: 20px;
            border: 1px solid #d32f2f;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d32f2f;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px 15px;
            background-color: #d32f2f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #c62828;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .zamowienia-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            width: 100%;
        }

        .sticky-note {
            position: relative;
            background-color: #fdfd96;
            padding: 15px;
            margin: 10px;
            width: 300px;
            border-radius: 8px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .sticky-note input[type="checkbox"] {
            position: absolute;
            top: 10px;
            right: 10px;
            transform: translateY(-50%);
        }
    </style>
    <script>
        // JavaScript do dynamicznej obsługi formularza
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const statusText = this.nextElementSibling;
                    if (this.checked) {
                        statusText.textContent = 'Zrealizowane';
                    } else {
                        statusText.textContent = 'Niezrealizowane';
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="login-container">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <form action="login.php" method="POST">
                <h2>Zaloguj się</h2>
                <?php if ($loginError): ?>
                    <p style="color: red;"><?php echo $loginError; ?></p>
                <?php endif; ?>
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" required>
                <label for="haslo">Hasło:</label>
                <input type="password" id="haslo" name="haslo" required>
                <button type="submit">Zaloguj</button>
            </form>
        <?php else: ?>
            <h2>Wszystkie Zamówienia</h2>
            <form method="POST" action="login.php">
                <div class="zamowienia-container">
                    <?php if (!empty($zamowienia)): ?>
                        <?php foreach ($zamowienia as $zamowienie): ?>
                            <div class="sticky-note">
                                <div><strong>Zamówienie #<?php echo htmlspecialchars($zamowienie['id_zamowienia']); ?></strong></div>
                                <p><strong>Imię:</strong> <?php echo htmlspecialchars($zamowienie['imie']); ?></p>
                                <p><strong>Nazwisko:</strong> <?php echo htmlspecialchars($zamowienie['nazwisko']); ?></p>
                                <p><strong>Telefon:</strong> <?php echo htmlspecialchars($zamowienie['nr_telefonu']); ?></p>
                                <p><strong>Adres:</strong> <?php echo htmlspecialchars($zamowienie['adres_zamieszkania']); ?></p>
                                <p><strong>Łączna Cena:</strong> <?php echo htmlspecialchars($zamowienie['total_price']); ?> PLN</p>
                                <p><strong>Data:</strong> <?php echo htmlspecialchars($zamowienie['created_at']); ?></p>
                                <p><strong>Typ:</strong> <?php echo htmlspecialchars($zamowienie['metoda_dostawy']); ?></p>
                                <label>
                                    <input type="hidden" name="status[<?php echo htmlspecialchars($zamowienie['id_zamowienia']); ?>]" value="0">
                                    <input type="checkbox" 
                                        name="status[<?php echo htmlspecialchars($zamowienie['id_zamowienia']); ?>]" 
                                        value="1" 
                                        <?php echo $zamowienie['is_completed'] ? 'checked' : ''; ?>>
                                    <span><?php echo $zamowienie['is_completed'] ? 'Zrealizowane' : 'Niezrealizowane'; ?></span>
                                </label>
                                <p><strong>Pizze:</strong><br>
                                    <?php
                                        // Pobranie pizz z zamówienia
                                        $stmtPizzas = $pdo->prepare("SELECT p.pizza AS pizza, zp.ilosc, p.id_pizzy FROM zamowienia_pizza zp JOIN pizza p ON zp.id_pizzy = p.id_pizzy WHERE zp.zamowienie_id = :order_id");
                                        $stmtPizzas->bindParam(':order_id', $zamowienie['id_zamowienia']);
                                        $stmtPizzas->execute();
                                        $pizzas = $stmtPizzas->fetchAll(PDO::FETCH_ASSOC);

                                        if (!empty($pizzas)): 
                                            foreach ($pizzas as $pizza): ?>
                                                <?php echo htmlspecialchars($pizza['pizza']) . " (Ilość: " . htmlspecialchars($pizza['ilosc']) . ")<br>"; ?>
                                            <?php endforeach; 
                                        else: ?>
                                            Brak zamówionych pizz.<br>
                                        <?php endif; ?>
                                </p>

                                <p><strong>Dodatkowe składniki:</strong><br>
                                    <?php
                                        // Pobranie dodatkowych składników dla każdej pizzy
                                        foreach ($pizzas as $pizza) {
                                            $stmtAdditionalIngredients = $pdo->prepare("SELECT s.nazwa_skladnika FROM pizza_skladniki ps JOIN skladniki s ON ps.id_skladnika = s.id_skladnika WHERE ps.id_pizzy = :pizza_id");
                                            $pizzaId = $pizza['id_pizzy'];
                                            $stmtAdditionalIngredients->bindParam(':pizza_id', $pizzaId);
                                            $stmtAdditionalIngredients->execute();
                                            $dodatkowe_skladniki = $stmtAdditionalIngredients->fetchAll(PDO::FETCH_COLUMN);
                                            echo htmlspecialchars(implode(', ', $dodatkowe_skladniki)) . "<br>";
                                        }
                                    ?>
                                </p>

                                <p><strong>Napoje:</strong><br>
                                    <?php
                                        // Pobranie napojów
                                        $stmtDrinks = $pdo->prepare("SELECT n.napoj AS napoj, zn.ilosc FROM zamowienia_napoj zn JOIN napoj n ON zn.napoj_id = n.id_napoju WHERE zn.zamowienie_id = :order_id");
                                        $stmtDrinks->bindParam(':order_id', $zamowienie['id_zamowienia']);
                                        $stmtDrinks->execute();
                                        $napoje = $stmtDrinks->fetchAll(PDO::FETCH_ASSOC);

                                        if (!empty($napoje)): 
                                            foreach ($napoje as $napoj): ?>
                                                <?php echo htmlspecialchars($napoj['napoj']) . " (Ilość: " . htmlspecialchars($napoj['ilosc']) . ")<br>"; ?>
                                            <?php endforeach; 
                                        else: ?>
                                            Brak zamówionych napojów.<br>
                                        <?php endif; ?>
                                </p>

                                <p><strong>Sosy:</strong><br>
                                    <?php
                                        // Pobranie sosów
                                        $stmtSosy = $pdo->prepare("SELECT s.sos AS sos, zs.ilosc FROM zamowienia_sos zs JOIN sos s ON zs.sos_id = s.id_sosu WHERE zs.zamowienie_id = :order_id");
                                        $stmtSosy->bindParam(':order_id', $zamowienie['id_zamowienia']);
                                        $stmtSosy->execute();
                                        $sosy = $stmtSosy->fetchAll(PDO::FETCH_ASSOC);

                                        if (!empty($sosy)): 
                                            foreach ($sosy as $sos): ?>
                                                <?php echo htmlspecialchars($sos['sos']) . " (Ilość: " . htmlspecialchars($sos['ilosc']) . ")<br>"; ?>
                                            <?php endforeach; 
                                        else: ?>
                                            Brak zamówionych sosów.<br>
                                        <?php endif; ?>
                                </p>
                                <!-- Lista rozwijana pracowników -->
                                <p><strong>Pracownik:</strong></p>
                                <select name="pracownik[<?php echo htmlspecialchars($zamowienie['id_zamowienia']); ?>]">
                                    <?php foreach ($pracownicy as $pracownik): ?>
                                        <option value="<?php echo $pracownik['id_pracownik']; ?>" 
                                            <?php echo ($zamowienie['id_pracownik'] == $pracownik['id_pracownik']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pracownik['pracownik']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Brak zamówień.</p>
                    <?php endif; ?>
                </div>
                <button type="submit" name="update_status">Zaktualizuj status</button>
            </form>
            <form action="logout.php" method="POST">
                <button type="submit">Wyloguj</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
