<?php
// Połączenie z bazą danych
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pizzeria";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Połączenie nieudane: " . $conn->connect_error);
}

// Pobranie wybranej metody sortowania z formularza
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Definiowanie domyślnych zapytań SQL
$pizza_query = "SELECT id_pizzy, pizza, cena_pizzy FROM pizza";
$napoj_query = "SELECT id_napoju, napoj, cena_napoju FROM napoj";
$sos_query = "SELECT id_sosu, sos, cena_sosu FROM sos";
$skladniki_query = "SELECT id_skladnika, nazwa_skladnika, cena_skladnika FROM skladniki";

// Modyfikacja zapytań w zależności od wybranej opcji sortowania
switch ($sort_option) {
    case 'price_asc':
        $pizza_query .= " ORDER BY cena_pizzy ASC";
        $napoj_query .= " ORDER BY cena_napoju ASC";
        $sos_query .= " ORDER BY cena_sosu ASC";
        break;
    case 'price_desc':
        $pizza_query .= " ORDER BY cena_pizzy DESC";
        $napoj_query .= " ORDER BY cena_napoju DESC";
        $sos_query .= " ORDER BY cena_sosu DESC";
        break;
    case 'alpha':
        $pizza_query .= " ORDER BY pizza ASC";
        $napoj_query .= " ORDER BY napoj ASC";
        $sos_query .= " ORDER BY sos ASC";
        break;
    default:
        // Brak sortowania lub sortowanie domyślne
        break;
}

// Pobranie danych z bazy z odpowiednim sortowaniem
$pizza_result = $conn->query($pizza_query);
$napoj_result = $conn->query($napoj_query);
$sos_result = $conn->query($sos_query);
$skladniki_result = $conn->query($skladniki_query);

// Przekształć składniki w format JSON
$skladniki = [];
while ($row = $skladniki_result->fetch_assoc()) {
    $skladniki[] = $row;
}
$skladniki_json = json_encode($skladniki);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Pizzeria</title>
    <link rel="stylesheet" type="text/css" href="styl.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .menu-container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        .menu-item {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        select, input[type="number"], input[type="text"], textarea {
            margin-bottom: 10px;
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .order-button {
            padding: 10px 15px;
            background-color: #d32f2f;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .order-button:hover {
            background-color: #b71c1c;
        }
    </style>
</head>
<body>
<header>
    <h1>Menu</h1>
    <nav>
        <ul>
            <li><a href="index.php" class="order-button">Strona Główna</a></li>
            <li><a href="menu.php" class="order-button">Menu</a></li>
            <li><a href="contact.php" class="order-button">Kontakt</a></li>
        </ul>
    </nav>
</header>

<div class="menu-container">
    <form action="submit_order.php" method="POST">
        <h2>Wybierz metodę sortowania:</h2>
        <label for="sort">Sortuj według:</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
            <option value="default" <?= $sort_option == 'default' ? 'selected' : '' ?>>Domyślnie</option>
            <option value="price_asc" <?= $sort_option == 'price_asc' ? 'selected' : '' ?>>Cena: rosnąco</option>
            <option value="price_desc" <?= $sort_option == 'price_desc' ? 'selected' : '' ?>>Cena: malejąco</option>
            <option value="alpha" <?= $sort_option == 'alpha' ? 'selected' : '' ?>>Alfabetycznie</option>
        </select>

        <h2>Wybierz pizzę:</h2>
        <div id="pizzaContainer">
            <div class="menu-item">
                <label for="pizzaType">Pizza:</label>
                <select name="pizzaType[]">
                    <?php while ($row = $pizza_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_pizzy'] ?>"><?= $row['pizza'] ?> (<?= $row['cena_pizzy'] ?> zł)</option>
                    <?php endwhile; ?>
                </select>
                <label for="pizzaQuantity">Ilość:</label>
                <input type="number" name="pizzaQuantity[]" min="1" value="1">
            </div>
        </div>
        <button type="button" onclick="addPizza()">Dodaj kolejną pizzę</button>

        <h2>Wybierz składniki do pizzy:</h2>
        <div id="skladnikiContainer">
            <div class="menu-item">
                <label for="skladnikType">Składnik:</label>
                <select name="skladnikType[]">
                    <?php foreach ($skladniki as $skladnik): ?>
                        <option value="<?= $skladnik['id_skladnika'] ?>"><?= $skladnik['nazwa_skladnika'] ?> (<?= $skladnik['cena_skladnika'] ?> zł)</option>
                    <?php endforeach; ?>
                </select>
                <button type="button" onclick="removeIngredient(this)">Usuń składnik</button>
            </div>
        </div>
        <button type="button" onclick="addSkladnik()">Dodaj składnik</button>

        <h2>Wybierz napój:</h2>
        <div id="napojContainer">
            <div class="menu-item">
                <label for="napojType">Napój:</label>
                <select name="napojType[]">
                    <?php while ($row = $napoj_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_napoju'] ?>"><?= $row['napoj'] ?> (<?= $row['cena_napoju'] ?> zł)</option>
                    <?php endwhile; ?>
                </select>
                <label for="napojQuantity">Ilość:</label>
                <input type="number" name="napojQuantity[]" min="1" value="1">
            </div>
        </div>
        <button type="button" onclick="addNapoj()">Dodaj kolejny napój</button>

        <h2>Wybierz sos:</h2>
        <div id="sosContainer">
            <div class="menu-item">
                <label for="sosType">Sos:</label>
                <select name="sosType[]">
                    <?php while ($row = $sos_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_sosu'] ?>"><?= $row['sos'] ?> (<?= $row['cena_sosu'] ?> zł)</option>
                    <?php endwhile; ?>
                </select>
                <label for="sosQuantity">Ilość:</label>
                <input type="number" name="sosQuantity[]" min="1" value="1">
            </div>
        </div>
        <h2>Dane kontaktowe:</h2>
            <label for="miasto">Miasto:</label>
            <input type="text" name="miasto" required oninput="validateText(this)" placeholder="Wprowadź miasto">

            <label for="ulica">Ulica:</label>
            <input type="text" name="ulica" required oninput="validateText(this)" placeholder="Wprowadź ulice">

            <label for="numer_domu">Numer domu:</label>
            <input type="text" name="numer_domu" required placeholder="Wprowadź numer domu">

            <label for="numer_lokalu">Numer lokalu (opcjonalnie):</label>
            <input type="text" name="numer_lokalu" placeholder="Wprowadź numer lokalu">

            <label for="kod_pocztowy">Kod pocztowy:</label>
            <input type="text" name="kod_pocztowy" required oninput="validatePostalCode(this)" pattern="\d{2}-\d{3}" title="Proszę wpisać kod pocztowy w formacie 00-000" placeholder="Wprowadź kod pocztowy">

            <label for="imie">Imię:</label>
            <input type="text" name="imie" required oninput="validateText(this)" pattern="[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ]+" title="Proszę wpisać poprawne imię (tylko litery)" placeholder="Wprowadź imie">

            <label for="nazwisko">Nazwisko:</label>
            <input type="text" name="nazwisko" required oninput="validateText(this)" pattern="[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ]+" title="Proszę wpisać poprawne nazwisko (tylko litery)" placeholder="Wprowadź nazwisko">

            <label for="nr_telefonu">Numer telefonu:</label>
            <input type="text" name="nr_telefonu" required placeholder="Wprowadź numer telefonu">

            <input type="submit" value="Złóż zamówienie" class="order-button">	
    </form>
</div>

<script>
    function addPizza() {
        const container = document.getElementById('pizzaContainer');
        const newDiv = document.createElement('div');
        newDiv.className = 'menu-item';
        newDiv.innerHTML = `
            <label for="pizzaType">Pizza:</label>
            <select name="pizzaType[]">
                <?php $pizza_result->data_seek(0); while ($row = $pizza_result->fetch_assoc()): ?>
                    <option value="<?= $row['id_pizzy'] ?>"><?= $row['pizza'] ?> (<?= $row['cena_pizzy'] ?> zł)</option>
                <?php endwhile; ?>
            </select>
            <label for="pizzaQuantity">Ilość:</label>
            <input type="number" name="pizzaQuantity[]" min="1" value="1">
        `;
        container.appendChild(newDiv);
    }

    function addSkladnik() {
        const container = document.getElementById('skladnikiContainer');
        const newDiv = document.createElement('div');
        newDiv.className = 'menu-item';
        newDiv.innerHTML = `
            <label for="skladnikType">Składnik:</label>
            <select name="skladnikType[]">
                <?php foreach ($skladniki as $skladnik): ?>
                    <option value="<?= $skladnik['id_skladnika'] ?>"><?= $skladnik['nazwa_skladnika'] ?> (<?= $skladnik['cena_skladnika'] ?> zł)</option>
                <?php endforeach; ?>
            </select>
            <button type="button" onclick="removeIngredient(this)">Usuń składnik</button>
        `;
        container.appendChild(newDiv);
    }

    function addNapoj() {
        const container = document.getElementById('napojContainer');
        const newDiv = document.createElement('div');
        newDiv.className = 'menu-item';
        newDiv.innerHTML = `
            <label for="napojType">Napój:</label>
            <select name="napojType[]">
                <?php $napoj_result->data_seek(0); while ($row = $napoj_result->fetch_assoc()): ?>
                    <option value="<?= $row['id_napoju'] ?>"><?= $row['napoj'] ?> (<?= $row['cena_napoju'] ?> zł)</option>
                <?php endwhile; ?>
            </select>
            <label for="napojQuantity">Ilość:</label>
            <input type="number" name="napojQuantity[]" min="1" value="1">
        `;
        container.appendChild(newDiv);
    }

    function removeIngredient(button) {
        const ingredientDiv = button.parentElement;
        ingredientDiv.parentElement.removeChild(ingredientDiv);
    }
</script>

</body>
<footer>
    <div class="footer-links">
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="contact.php">Kontakt</a>
    </div>
    <p>&copy; 2024 Pizzeria. Wszelkie prawa zastrzeżone.</p>
</footer>
</html>

<?php
$conn->close();
?>
