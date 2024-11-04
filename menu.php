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
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        let koszyk = [];
        let skladnikiData = <?php echo $skladniki_json; ?>;
        // Funkcja dodająca produkt do koszyka
        function dodajDoKoszyka() {
        koszyk = [];
        let total = 0; // Initialize total price

        // Loop through pizza items in the menu
        const pizzaItems = document.querySelectorAll('#pizzaContainer .menu-item');
        pizzaItems.forEach(item => {
            const pizzaId = item.querySelector('select[name="pizzaType[]"]').value;
            const pizzaName = item.querySelector('select[name="pizzaType[]"] option:checked').textContent;
            const pizzaQuantity = parseInt(item.querySelector('input[name="pizzaQuantity[]"]').value);

            if (pizzaId && pizzaQuantity > 0) {
                console.log(`Adding pizza: ${pizzaName} (ID: ${pizzaId}) x ${pizzaQuantity}`);

                // Gather selected ingredients for this pizza
                const selectedIngredients = Array.from(item.querySelectorAll('.skladnikiContainer .skladnikSelect'))
                    .map(select => {
                        const skladnikId = select.value;
                        const skladnikName = select.options[select.selectedIndex].text;

                        // Find the corresponding ingredient price using the ID
                        const skladnik = skladnikiData.find(s => s.id_skladnika == skladnikId);
                        const skladnikPrice = skladnik ? parseFloat(skladnik.cena_skladnika) : 0;

                        // Log the ingredient being added and its price
                        console.log(`  Ingredient: ${skladnikName} (ID: ${skladnikId}) - Price: ${skladnikPrice} PLN`);

                        // Add the price of the ingredient to the total price based on quantity
                        total += skladnikPrice * pizzaQuantity;

                        return { id: skladnikId, name: skladnikName, price: skladnikPrice };
                    });

                // Add this pizza with its ingredients to the cart
                koszyk.push({
                    typ: 'Pizza',
                    id: pizzaId,
                    nazwa: pizzaName,
                    ilosc: pizzaQuantity,
                    skladniki: selectedIngredients
                });

                // Add the base price of the pizza to the total price
                const pizzaPrice = parseFloat(item.getAttribute('data-price')) || 0;
                console.log(`  Base pizza price: ${pizzaPrice} PLN`);
                total += pizzaPrice * pizzaQuantity;
            }
        });
            // Zbieranie danych napojów
            const napojItems = document.querySelectorAll('#napojContainer .menu-item');
    napojItems.forEach(item => {
        const napojId = item.querySelector('select[name="napojType[]"]').value;
        const napojName = item.querySelector('select[name="napojType[]"] option:checked').textContent;
        const napojQuantity = parseInt(item.querySelector('input[name="napojQuantity[]"]').value);

        if (napojId && napojQuantity > 0) {
            koszyk.push({
                typ: 'Napój',
                id: napojId,
                nazwa: napojName,
                ilosc: napojQuantity
            });
        }
    });

            // Zbieranie danych sosów
            const sosItems = document.querySelectorAll('#sosContainer .menu-item');
    sosItems.forEach(item => {
        const sosId = item.querySelector('select[name="sosType[]"]').value;
        const sosName = item.querySelector('select[name="sosType[]"] option:checked').textContent;
        const sosQuantity = parseInt(item.querySelector('input[name="sosQuantity[]"]').value);

        if (sosId && sosQuantity > 0) {
            koszyk.push({
                typ: 'Sos',
                id: sosId,
                nazwa: sosName,
                ilosc: sosQuantity
            });
        }
    });

            // Aktualizuj widok koszyka
            aktualizujKoszyk();
        }

        // Funkcja aktualizująca widok koszyka
        function aktualizujKoszyk() {
    const koszykContainer = document.getElementById('koszyk');
    koszykContainer.innerHTML = '<h3>Twój koszyk</h3>';

    if (koszyk.length === 0) {
        koszykContainer.innerHTML += '<p>Koszyk jest pusty.</p>';
        return;
    }

    koszyk.forEach((produkt, indeks) => {
        let skladnikiHTML = '';

        if (produkt.skladniki && produkt.skladniki.length > 0) {
            skladnikiHTML = '<ul>';
            produkt.skladniki.forEach(skladnik => {
                skladnikiHTML += `<li>${skladnik.name} - ${skladnik.price.toFixed(2)} PLN</li>`;
            });
            skladnikiHTML += '</ul>';
        }

        koszykContainer.innerHTML += `
            <div class="koszyk-item">
                <p>${produkt.typ}: ${produkt.nazwa} - Ilość: ${produkt.ilosc}</p>
                ${skladnikiHTML}
            </div>
        `;
    });

    // Obliczanie całkowitej ceny
    const total = koszyk.reduce((sum, produkt) => {
        let productPrice = 0;

        // Calculate price for pizza with ingredients
        if (produkt.typ === 'Pizza') {
            // Price of ingredients
            const ingredientsPrice = produkt.skladniki.reduce((skladnikiSum, skladnik) => {
                return skladnikiSum + (skladnik.price * produkt.ilosc);
            }, 0);

            // Price of the pizza itself
            const priceText = produkt.nazwa.match(/\((\d+(?:\.\d+)?) zł\)/);
            const pizzaPrice = priceText ? parseFloat(priceText[1]) : 0;

            productPrice = (pizzaPrice * produkt.ilosc) + ingredientsPrice;
        } else {
            // Handle drinks and sauces
            const priceText = produkt.nazwa.match(/\((\d+(?:\.\d+)?) zł\)/);
            const itemPrice = priceText ? parseFloat(priceText[1]) : 0;

            productPrice = itemPrice * produkt.ilosc;
        }

        return sum + productPrice;
    }, 0);

            console.log(`Total price: ${total} PLN`);
            koszykContainer.innerHTML += `<p>Łączna cena: ${total.toFixed(2)} PLN</p>`;
        }

        // Funkcja usuwająca produkt z koszyka
        //function usunZKoszyka(indeks) {
        //    koszyk.splice(indeks, 1);
        //   aktualizujKoszyk();
        //}
        console.log('Skladniki Data:', skladnikiData);
    </script>
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

<div class="menu-container">
    <form action="submit_order.php" method="POST" onsubmit="dodajDoKoszyka(); return przygotujZamowienie();">
        
        <h2>Wybierz pizzę:</h2>
        <div id="pizzaContainer">
            <div class="menu-item">
                <label for="pizzaType">Pizza:</label>
                <select name="pizzaType[]" onchange="dodajDoKoszyka(); toggleSkladniki(this)">
                    <option value="">Wybierz pizzę</option>
                    <?php while ($row = $pizza_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_pizzy'] ?>"><?= $row['pizza'] ?> (<?= $row['cena_pizzy'] ?> zł)</option>
                    <?php endwhile; ?>
                </select>
                <label for="pizzaQuantity">Ilość:</label>
                <input type="number" name="pizzaQuantity[]" min="0" value="0" onchange="dodajDoKoszyka(); checkPizzaQuantity(this)">

                <div class="skladnikiContainer hidden">
                    <label>Składniki:</label>
                    <div class="skladnikItem">
                        <select name="skladnikiType[][id]" class="skladnikSelect">
                            <option value="">Wybierz składnik</option>
                            <?php foreach ($skladniki as $skladnik): ?>
                                <option value="<?= $skladnik['id_skladnika'] ?>"><?= $skladnik['nazwa_skladnika'] ?> (<?= $skladnik['cena_skladnika'] ?> zł)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="button" class="add-skladnik-button" onclick="addSkladnik(this)">Dodaj składnik</button>
                </div>
            </div>
        </div>
        <button type="button" id="addPizzaButton" onclick="addPizza()">Dodaj kolejną pizzę</button>
    
        <h2>Wybierz napój:</h2>
        <div id="napojContainer">
            <div class="menu-item">
                <label for="napojType">Napój:</label>
                <select name="napojType[]" onchange="dodajDoKoszyka()">
                <option value="">Wybierz napój</option>
                    <?php while ($row = $napoj_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_napoju'] ?>"><?= $row['napoj'] ?> (<?= $row['cena_napoju'] ?> zł)</option>
                    <?php endwhile; ?>
                </select>
                <label for="napojQuantity">Ilość:</label>
                <input type="number" name="napojQuantity[]" min="0" value="0" onchange="dodajDoKoszyka()">
            </div>
        </div>
        <button type="button" id="addNapojButton" onclick="addNapoj()">Dodaj kolejny napój</button>

        <h2>Wybierz sos:</h2>
        <div id="sosContainer">
            <div class="menu-item">
                <label for="sosType">Sos:</label>
                <select name="sosType[]" onchange="dodajDoKoszyka()">
                <option value="">Wybierz sos</option>
                    <?php while ($row = $sos_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_sosu'] ?>"><?= $row['sos'] ?> (<?= $row['cena_sosu'] ?> zł)</option>
                    <?php endwhile; ?>
                </select>
                <label for="sosQuantity">Ilość:</label>
                <input type="number" name="sosQuantity[]" min="0" value="0" onchange="dodajDoKoszyka()">
            </div>
        </div>
        <button type="button" onclick="addSos()">Dodaj kolejny sos</button>

        <h2>Dostawa/odbiór</h2>
                <label for="metoda_dostawy">Wybierz metodę dostawy:</label>
            <select id="metoda_dostawy" name="metoda_dostawy" onchange="toggleFormFields()">
                <option value="odbior">Odbiór osobisty</option>
                <option value="dostawa">Dostawa</option>
            </select>
        <h2>Dane kontaktowe:</h2>


<form method="post">
    <label for="imie">Imię:</label>
    <input type="text" name="imie" required oninput="validateName(this)" pattern="[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ]+" title="Proszę wpisać poprawne imię (tylko litery)" placeholder="Wprowadź imię">

    <label for="nazwisko">Nazwisko:</label>
    <input type="text" name="nazwisko" required oninput="validateName(this)" pattern="[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ]+" title="Proszę wpisać poprawne nazwisko (tylko litery)" placeholder="Wprowadź nazwisko">

    <label for="nr_telefonu">Numer telefonu:</label>
    <input type="text" id="nr_telefonu" name="nr_telefonu" required oninput="validatePhoneNumber(this)" placeholder="Wprowadź numer telefonu">
<div id="deliveryFields" style="display: none;">
    <label for="miasto">Miasto:</label>
    <input type="text" name="miasto" oninput="validateCity(this)" placeholder="Wprowadź miasto">

    <label for="ulica">Ulica:</label>
    <input type="text" name="ulica" oninput="validateStreet(this)" placeholder="Wprowadź ulicę">

    <label for="numer_domu">Numer domu:</label>
    <input type="text" name="numer_domu" oninput="validateHouseNumber(this)" placeholder="Wprowadź numer domu">

    <label for="numer_lokalu">Numer lokalu (opcjonalnie):</label>
    <input type="text" name="numer_lokalu" oninput="validateApartmentNumber(this)" placeholder="Wprowadź numer lokalu">

    <label for="kod_pocztowy">Kod pocztowy:</label>
    <input type="text" id="kod_pocztowy" name="kod_pocztowy" oninput="validatePostalCode(this)" placeholder="Wprowadź kod pocztowy">
</div>
<div>
    <input type="submit" value="Złóż zamówienie" class="order-button">	
</div>
</form>
<div id="koszyk" class="koszyk-container">
        <p>Koszyk jest pusty.</p>
    </div>


                    </body>

<script>
    function toggleSkladniki(select) {
        const skladnikiContainer = select.closest('.menu-item').querySelector('.skladnikiContainer');
        skladnikiContainer.classList.toggle('hidden', select.value === '');
    }

    function checkPizzaQuantity(input) {
        const quantity = parseInt(input.value);
        const skladnikiContainer = input.closest('.menu-item').querySelector('.skladnikiContainer');
        if (quantity === 0) {
            skladnikiContainer.classList.add('hidden');
        } else {
            skladnikiContainer.classList.remove('hidden');
        }
    }

    function addPizza() {
        const pizzaContainer = document.getElementById('pizzaContainer');
        const newPizza = document.createElement('div');
        newPizza.classList.add('menu-item');
        newPizza.innerHTML = `
            <label for="pizzaType">Pizza:</label>
            <select name="pizzaType[]" onchange="toggleSkladniki(this)">
                <option value="">Wybierz pizzę</option>
                
                <?php
                $pizza_result->data_seek(0); // Resetujemy wskaźnik do początku
                while ($row = $pizza_result->fetch_assoc()): ?>
                    <option value="<?= $row['id_pizzy'] ?>"><?= $row['pizza'] ?> (<?= $row['cena_pizzy'] ?> zł)</option>
                <?php endwhile; ?>
            </select>
            <label for="pizzaQuantity">Ilość:</label>
            <input type="number" name="pizzaQuantity[]" min="0" value="0" onchange="checkPizzaQuantity(this)">
            <div class="skladnikiContainer hidden">
                <label>Składniki:</label>
                <div class="skladnikItem">
                    <select name="skladnikiType[][id]" class="skladnikSelect">
                        <option value="">Wybierz składnik</option>
                        <!-- Poniżej musisz wstawić PHP do generowania opcji składników -->
                        <?php foreach ($skladniki as $skladnik): ?>
                            <option value="<?= $skladnik['id_skladnika'] ?>"><?= $skladnik['nazwa_skladnika'] ?> (<?= $skladnik['cena_skladnika'] ?> zł)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" class="add-skladnik-button" onclick="addSkladnik(this)">Dodaj składnik</button>
            </div>
        `;
        pizzaContainer.appendChild(newPizza);
    }

    function addSkladnik(button) {
        const skladnikItem = button.previousElementSibling;
        const newSkladnik = document.createElement('div');
        newSkladnik.classList.add('skladnikItem');
        newSkladnik.innerHTML = `
            <select name="skladnikiType[][id]" class="skladnikSelect">
                <option value="">Wybierz składnik</option>
                <?php foreach ($skladniki as $skladnik): ?>
                    <option value="<?= $skladnik['id_skladnika'] ?>"><?= $skladnik['nazwa_skladnika'] ?> (<?= $skladnik['cena_skladnika'] ?> zł)</option>
                <?php endforeach; ?>
            </select>
        `;
        skladnikItem.appendChild(newSkladnik);
    }

    function addNapoj() {
        const napojContainer = document.getElementById('napojContainer');
        const newNapoj = document.createElement('div');
        newNapoj.classList.add('menu-item');
        newNapoj.innerHTML = `
            <label for="napojType">Napój:</label>
            <select name="napojType[]">
            <option value="">Wybierz napój</option>
                <?php
                $napoj_result->data_seek(0); // Resetujemy wskaźnik do początku
                while ($row = $napoj_result->fetch_assoc()): ?>
                    <option value="<?= $row['id_napoju'] ?>"><?= $row['napoj'] ?> (<?= $row['cena_napoju'] ?> zł)</option>
                <?php endwhile; ?>
            </select>
            <label for="napojQuantity">Ilość:</label>
            <input type="number" name="napojQuantity[]" min="0" value="0">
        `;
        napojContainer.appendChild(newNapoj);
    }

    function addSos() {
        const sosContainer = document.getElementById('sosContainer');
        const newSos = document.createElement('div');
        newSos.classList.add('menu-item');
        newSos.innerHTML = `
            <label for="sosType">Sos:</label>
            <select name="sosType[]">
            <option value="">Wybierz sos</option>
                <?php
                $sos_result->data_seek(0); // Resetujemy wskaźnik do początku
                while ($row = $sos_result->fetch_assoc()): ?>
                    <option value="<?= $row['id_sosu'] ?>"><?= $row['sos'] ?> (<?= $row['cena_sosu'] ?> zł)</option>
                <?php endwhile; ?>
            </select>
            <label for="sosQuantity">Ilość:</label>
            <input type="number" name="sosQuantity[]" min="0" value="0">
        `;
        sosContainer.appendChild(newSos);
    }
    function validateCity(input) {
    // Sprawdza, czy wartość zawiera tylko litery i spacje
    if (!input.value.match(/^[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/)) {
        alert("Proszę wpisać poprawną nazwę miasta (tylko litery i spacje).");
        input.value = '';
    }
}

function validateStreet(input) {
    // Sprawdza, czy wartość zawiera tylko litery, cyfry, myślniki i spacje
    if (!input.value.match(/^[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\s\-]+$/)) {
        alert("Proszę wpisać poprawną nazwę ulicy (tylko litery, cyfry, myślniki i spacje).");
        input.value = '';
    }
}

function validateHouseNumber(input) {
    // Sprawdza, czy wartość zawiera cyfry i opcjonalnie litery (np. 12, 12A)
    if (!input.value.match(/^[s0-9]+[A-Za-z]{0,1}$/)) {
        alert("Proszę wpisać poprawny numer domu (np. 12 lub 12A).");
        input.value = '';
    }
}

function validateApartmentNumber(input) {
    // Sprawdza, czy wartość zawiera tylko cyfry
    if (input.value && !input.value.match(/^[0-9]+$/)) {
        alert("Proszę wpisać poprawny numer lokalu (tylko cyfry).");
        input.value = '';
    }
}

function validatePostalCode(input) {
    // Pobiera wartość z pola
    let value = input.value;

    // Usuwa wszystkie znaki inne niż cyfry i myślnik
    value = value.replace(/[^0-9\-]/g, '');

    // Automatycznie wstawia myślnik po wpisaniu dwóch cyfr, jeśli go tam jeszcze nie ma
    if (value.length === 3 && !value.includes('-')) {
        value = value.slice(0, 2) + '-' + value.slice(2);
    }

    // Ogranicza długość do 6 znaków (dwa cyfry, myślnik, trzy cyfry)
    if (value.length > 6) {
        value = value.slice(0, 6);
    }

    // Ustawia przefiltrowaną wartość z powrotem do inputa
    input.value = value;
}


function validateName(input) {
    // Sprawdza, czy wartość zawiera tylko litery (polskie i inne) oraz ewentualnie pojedyncze spacje
    if (!input.value.match(/^[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/)) {
        alert("Proszę wpisać poprawne imię lub nazwisko (tylko litery).");
        input.value = '';
    }
}

function validatePhoneNumber(input) {
    // Pobiera wartość z pola i usuwa wszystkie znaki inne niż cyfry
    let value = input.value;

    // Usuwa wszystkie znaki inne niż cyfry
    value = value.replace(/[^0-9]/g, '');

    // Ogranicza długość do 9 cyfr
    if (value.length > 9) {
        value = value.slice(0, 9);
    }

    input.value = value;

    // Sprawdza, czy numer ma dokładnie 9 cyfr
    if (value.length === 9) {
        // Możesz dodać tutaj dodatkowe logiki, jeśli chcesz, np. zmienić kolor obramowania na zielony
        input.style.borderColor = "green";
    } else {
        // Jeśli liczba cyfr jest inna niż 9, zmienia obramowanie na czerwony
        input.style.borderColor = "red";
    }
}
function toggleFormFields() {
    const metoda_dostawy = document.getElementById('metoda_dostawy').value;
    const deliveryFields = document.getElementById('deliveryFields');

    if (metoda_dostawy === 'dostawa') {
        deliveryFields.style.display = 'block';
    } else {
        deliveryFields.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', toggleFormFields);
</script>
<footer>
        <div class="footer-links">
            <a href="menu.php">Menu</a>
            <a href="contact.php">Kontakt</a>
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
