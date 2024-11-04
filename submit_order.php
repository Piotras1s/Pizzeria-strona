<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zamówienie Pizzy</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(90deg, rgba(0,255,0,1) 0%, rgba(255,250,240,1) 50%, rgba(255,0,0,1) 100%);
        }

        .content-wrapper {
            display: flex;
            flex: 1;
            justify-content: center;
            align-items: center;
            background-color: #fff;
        }

        .success-message {
            background-color: #fff;
            border: none;
            border-radius: 8px;
            max-width: 400px;
            padding: 20px;
            text-align: center;
            font-size: 18px;
            color: #3e2723;
            box-shadow: none;
        }

        .order-price {
            font-size: 20px;
            color: #3e2723;
            font-weight: bold;
            margin-top: 10px;
        }

        footer {
            background-color: #3e2723;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            width: 100%;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .footer-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #ffe0b2;
            font-weight: bold;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: #ffeb3b;
        }

        .social-icons {
            margin: 15px 0;
        }

        .social-icons a {
            margin: 0 10px;
            text-decoration: none;
            color: #ffe0b2;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: #ffeb3b;
        }

        .social-icons i {
            font-size: 20px;
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <h1>Menu</h1>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php" class="order-button">Strona Główna</a></li>
                <li><a href="menu.php" class="order-button">Menu</a></li>
                <li><a href="contact.php" class="order-button">Kontakt</a></li>
            </ul>
        </nav>
    </div>
</header>
    <div class="content-wrapper">
        <?php
        session_start();

        // Sprawdź, czy zamówienie zostało złożone
        if (isset($_SESSION['order_submitted']) && $_SESSION['order_submitted'] === true) {
            unset($_SESSION['order_submitted']); // Usuń flagę sesji
            header('Location: menu.php'); // Przekierowanie na stronę menu
            exit();
        }

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "pizzeria";

        // Połączenie z bazą danych
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Połączenie nieudane: " . $conn->connect_error);
        }

        // Przechwycenie danych zamówienia
        $pizze = $_POST['pizzaType'];
        $ilosc_pizzy = $_POST['pizzaQuantity'];
        $dodatkowe_skladniki = $_POST['skladnikiType'];
        $metoda_dostawy = $_POST['metoda_dostawy'];
        $miasto = $_POST['miasto'];
        $imie = $_POST['imie'];
        $nazwisko = $_POST['nazwisko'];
        $ulica = $_POST['ulica'];
        $numer_domu = $_POST['numer_domu'];
        $numer_lokalu = $_POST['numer_lokalu'] ?? null;
        $kod_pocztowy = $_POST['kod_pocztowy'];
        $numer_telefonu = $_POST['nr_telefonu'];

        $napoje = $_POST['napojType'];
        $ilosc_napoju = $_POST['napojQuantity'];
        $sosy = $_POST['sosType'];
        $ilosc_sosu = $_POST['sosQuantity'];

        // Inicjalizacja ceny całkowitej
        $total_price = 0;

        // Połączenie adresu w jeden string
        $adres_zamieszkania = "$miasto, $ulica, $numer_domu" . ($numer_lokalu ? "/$numer_lokalu" : "") . ", $kod_pocztowy";

        // Wstawienie zamówienia do tabeli "zamowienia" z total_price na początku
        $sql_zamowienie = "INSERT INTO zamowienia (imie, nazwisko, adres_zamieszkania, nr_telefonu, metoda_dostawy, total_price) 
                           VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_zamowienie);
        $stmt->bind_param("sssssd", $imie, $nazwisko, $adres_zamieszkania, $numer_telefonu, $metoda_dostawy ,$total_price);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Pobranie id zamówienia

        // Przetwarzanie składników dla pizzy
        if (count($pizze) > 0) {
            for ($i = 0; $i < count($pizze); $i++) {
                $pizza_id = $pizze[$i];
                $pizza_quantity = $ilosc_pizzy[$i] ?? 0;

                // Pobranie ceny pizzy
                $pizza_price_query = "SELECT cena_pizzy FROM pizza WHERE id_pizzy = ?";
                $stmt_price = $conn->prepare($pizza_price_query);
                $stmt_price->bind_param("i", $pizza_id);
                $stmt_price->execute();
                $pizza_price_result = $stmt_price->get_result();

                if ($pizza_price_result && $pizza_price_result->num_rows > 0) {
                    $pizza_price = $pizza_price_result->fetch_assoc()['cena_pizzy'];
                    $total_price += $pizza_price * $pizza_quantity;

                    // Wstawienie zamówionej pizzy do bazy
                    $conn->query("INSERT INTO zamowienia_pizza (zamowienie_id, id_pizzy, ilosc) 
                                  VALUES ('$order_id', '$pizza_id', '$pizza_quantity')");

                    // Przetwarzanie dodatkowych składników
                    if (isset($dodatkowe_skladniki[$i])) {
                        foreach ($dodatkowe_skladniki[$i] as $skladnik_id) {
                            if ($skladnik_id) {
                                // Pobranie ceny składnika
                                $skladnik_price_query = "SELECT cena_skladnika FROM skladniki WHERE id_skladnika = ?";
                                $stmt_skladnik = $conn->prepare($skladnik_price_query);
                                $stmt_skladnik->bind_param("i", $skladnik_id);
                                $stmt_skladnik->execute();
                                $skladnik_price_result = $stmt_skladnik->get_result();

                                if ($skladnik_price_result && $skladnik_price_result->num_rows > 0) {
                                    $skladnik_price = $skladnik_price_result->fetch_assoc()['cena_skladnika'];
                                    $total_price += $skladnik_price; // Dodaj cenę składnika do całkowitej ceny

                                    // Wstawienie dodatkowego składnika do bazy
                                    $conn->query("INSERT INTO zamowienia_skladniki (zamowienie_id, id_pizzy, id_skladnika) 
                                                  VALUES ('$order_id', '$pizza_id', '$skladnik_id')");
                                }
                            }
                        }
                    }
                }
            }
        }

        // Przetworzenie zamówionych napojów
        if (count($napoje) > 0) {
            for ($i = 0; $i < count($napoje); $i++) {
                $napoj_id = $napoje[$i];
                $napoj_quantity = $ilosc_napoju[$i] ?? 0;

                // Pobranie ceny napoju
                $napoj_price_query = "SELECT cena_napoju FROM napoj WHERE id_napoju = ?";
                $stmt_napoj = $conn->prepare($napoj_price_query);
                $stmt_napoj->bind_param("i", $napoj_id);
                $stmt_napoj->execute();
                $napoj_price_result = $stmt_napoj->get_result();

                if ($napoj_price_result && $napoj_price_result->num_rows > 0) {
                    $napoj_price = $napoj_price_result->fetch_assoc()['cena_napoju'];
                    $total_price += $napoj_price * $napoj_quantity; // Dodaj cenę napoju do całkowitej ceny

                    // Wstawienie zamówionego napoju do bazy
                    $conn->query("INSERT INTO zamowienia_napoj (zamowienie_id, napoj_id, ilosc) 
                                  VALUES ('$order_id', '$napoj_id', '$napoj_quantity')");
                }
            }
        }

        // Przetworzenie zamówionych sosów
        if (count($sosy) > 0) {
            for ($i = 0; $i < count($sosy); $i++) {
                $sos_id = $sosy[$i];
                $sos_quantity = $ilosc_sosu[$i] ?? 0;

                // Pobranie ceny sosu
                $sos_price_query = "SELECT cena_sosu FROM sos WHERE id_sosu = ?";
                $stmt_sos = $conn->prepare($sos_price_query);
                $stmt_sos->bind_param("i", $sos_id);
                $stmt_sos->execute();
                $sos_price_result = $stmt_sos->get_result();

                if ($sos_price_result && $sos_price_result->num_rows > 0) {
                    $sos_price = $sos_price_result->fetch_assoc()['cena_sosu'];
                    $total_price += $sos_price * $sos_quantity; // Dodaj cenę sosu do całkowitej ceny

                    // Wstawienie zamówionego sosu do bazy
                    $conn->query("INSERT INTO zamowienia_sos (zamowienie_id, sos_id, ilosc) 
                                  VALUES ('$order_id', '$sos_id', '$sos_quantity')");
                }
            }
        }

        // Aktualizacja całkowitej ceny zamówienia
        $update_order_query = "UPDATE zamowienia SET total_price = ? WHERE id_zamowienia = ?";
        $stmt_update = $conn->prepare($update_order_query);
        $stmt_update->bind_param("di", $total_price, $order_id);
        $stmt_update->execute();

        // Zakończenie zamówienia
        $_SESSION['order_submitted'] = true; // Ustawienie flagi sesji
        echo "<div class='success-message'>Zamówienie zostało złożone pomyślnie!</div>";
        echo "<div class='success-message'>Całkowita cena zamówienia: " . number_format($total_price, 2, ',', '') . " zł</div>";
        ?>
    </div>

    <footer>
        <div class="footer-links">
            <a href="index.php">Strona Główna</a>
            <a href="menu.php">Menu</a>
            <a href="contact.php">Kontakt</a>
        </div>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
    </footer>
</body>
</html>
