<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - Pizzeria</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    .content {
        text-align: center;
    }
</style>
</head>
<body>
    <?php
        $message = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $imie = htmlspecialchars(trim($_POST['imie']));
            $nazwisko = htmlspecialchars(trim($_POST['nazwisko']));
            $email = htmlspecialchars(trim($_POST['email']));
            $wiadomosc = htmlspecialchars(trim($_POST['wiadomosc']));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Proszę podać poprawny adres e-mail.";
            } else {
                $conn = new mysqli('localhost', 'root', '', 'pizzeria');

                if ($conn->connect_error) {
                    die("Błąd połączenia: " . $conn->connect_error);
                }
                $sql = "INSERT INTO kontakt (imie, nazwisko, email, wiadomosc) VALUES (?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $imie, $nazwisko, $email, $wiadomosc);

                if ($stmt->execute()) {
                    $message = "Wiadomość została wysłana pomyślnie!";
                } else {
                    $message = "Błąd: " . $stmt->error;
                }

                $stmt->close();
                $conn->close();
            }
        }
    ?>
<header>
    <div class="header-container">
        <nav class="main-nav">
            <ul>
                <li><a href="index.php" class="order-button">Strona Główna</a></li>
                <li><a href="menu.php" class="order-button">Menu</a></li>
                <li><a href="contact.php" class="order-button">Kontakt</a></li>
            </ul>
        </nav>
        <a href="menu.php" class="order-now-button">Zamów Teraz</a>
    </div>
</header>
    <main>
        <section class="contact-form content-wrapper"> <!-- Dodano klasę content-wrapper -->
            <div class="content">
            <h2>Wyślij Nam Wiadomość</h2>
            </div>
            <form action="" method="POST">
                <?php if ($message != ''): ?>
                    <p><?php echo $message; ?></p>
                <?php endif; ?>

                <label for="imie">Imię:</label>
                <input type="text" pattern="[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ]+" title="Proszę wpisać poprawne imię (tylko litery)"name="imie" id="imie" required>

                <label for="nazwisko">Nazwisko:</label>
                <input type="text" pattern="[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ]+" title="Proszę wpisać poprawne nazwisko (tylko litery)" name="nazwisko" id="nazwisko" required>

                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required>

                <label for="wiadomosc">Wiadomość:</label>
                <textarea name="wiadomosc" id="wiadomosc" rows="12" required></textarea>

                <button type="submit">Wyślij</button>
            </form>
        </section>
    </main>

    <footer>
        <div class="footer-links">
            <a href="index.php">Strona Główna</a>
            <a href="menu.php">Menu</a>
            <a href="privacy.php">Polityka prywatności</a>
        </div>
        <div class="social-icons">
            <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
        </div>
        <p>&copy; 2024 Pizzeria. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
