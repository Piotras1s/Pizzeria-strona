-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Wrz 30, 2024 at 12:06 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pizzeria`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kontakt`
--

CREATE TABLE `kontakt` (
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(70) NOT NULL,
  `email` varchar(100) NOT NULL,
  `wiadomosc` longtext NOT NULL,
  `id_kontaktu` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontakt`
--

INSERT INTO `kontakt` (`imie`, `nazwisko`, `email`, `wiadomosc`, `id_kontaktu`) VALUES
('qwertyu123456', '654321', 'eyz65254@nowni.com', 'lolololounuio', 8);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `napoj`
--

CREATE TABLE `napoj` (
  `id_napoju` int(3) NOT NULL,
  `napoj` varchar(50) NOT NULL,
  `cena_napoju` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `napoj`
--

INSERT INTO `napoj` (`id_napoju`, `napoj`, `cena_napoju`) VALUES
(1, 'woda_niegaz', 7.00),
(2, 'woda_gaz', 7.00),
(3, 'sok_pomaranczowy', 9.00),
(4, 'sok_jablkowy', 8.00),
(5, 'sok_grejpfrutowy', 13.00),
(6, 'lemoniada', 10.00),
(7, 'herbata', 11.00),
(8, 'kawa', 13.00),
(9, 'wino_biale', 23.00),
(10, 'wino_czerwone', 23.00),
(11, 'koktajl_owocowy', 23.00),
(12, 'smoothie(owocowe)', 13.00),
(14, 'piwo', 6.99);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pizza`
--

CREATE TABLE `pizza` (
  `id_pizzy` int(11) NOT NULL,
  `pizza` varchar(50) NOT NULL,
  `cena_pizzy` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pizza`
--

INSERT INTO `pizza` (`id_pizzy`, `pizza`, `cena_pizzy`) VALUES
(1, 'margharita', 25.00),
(2, 'pieczarkowa', 25.00),
(3, 'prosta', 27.00),
(4, 'hawajska', 28.00),
(5, 'tuńczyk', 24.00),
(6, 'broccoli', 29.00),
(7, 'salami', 31.00),
(8, 'czosnkowa', 35.00),
(9, 'diabolo', 32.00),
(10, 'wegetariańska', 27.00),
(11, 'oliwka', 37.00),
(12, 'grecka', 43.00),
(13, 'lesia', 23.00),
(14, 'serowa', 28.00),
(15, 'szpinakowa', 38.00),
(16, 'wiesjka', 34.00),
(17, 'bekon', 33.00),
(18, 'farmerska', 39.00),
(19, 'chicken', 41.00),
(20, 'śląska', 43.00),
(21, 'owoce_morza', 38.00),
(22, 'ogorkowa', 39.00),
(23, 'goralska', 28.00),
(24, 'light', 37.00),
(25, 'bolognese', 33.00),
(26, 'specjale', 43.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sos`
--

CREATE TABLE `sos` (
  `id_sosu` int(3) NOT NULL,
  `sos` varchar(50) NOT NULL,
  `cena_sosu` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sos`
--

INSERT INTO `sos` (`id_sosu`, `sos`, `cena_sosu`) VALUES
(1, 'czosnkowy', 5.00),
(2, 'ketchup', 5.00),
(3, 'pesto', 5.00),
(4, 'alfredo', 5.00),
(5, 'barbecue', 5.00),
(6, 'aioli', 5.00),
(7, 'chilli', 5.00),
(8, 'śmietanowy', 5.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia`
--

CREATE TABLE `zamowienia` (
  `id_zamowienia` int(11) NOT NULL,
  `imie` varchar(255) DEFAULT NULL,
  `nazwisko` varchar(255) DEFAULT NULL,
  `nr_telefonu` varchar(15) DEFAULT NULL,
  `adres_zamieszkania` text DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zamowienia`
--

INSERT INTO `zamowienia` (`id_zamowienia`, `imie`, `nazwisko`, `nr_telefonu`, `adres_zamieszkania`, `total_price`, `created_at`) VALUES
(1, 'pitor ', 'wiagn ', 'dvdfds q', 'fdddzmvkmzx', 93.00, '2024-09-27 06:23:53'),
(2, 'piotr', 'wita', '3925', 'fdsogs\r\n', 117.00, '2024-09-27 06:24:44'),
(3, 'qwerty', 'qwertyu', '123123123', '123456y', 37.00, '2024-09-27 06:38:31'),
(4, 'qwer', 'qwe', '123456123', 'Wodzislaw slaski, qwerty 12, 44-311', 37.00, '2024-09-27 06:48:28'),
(5, 'qwertyu', 'qwerty', '123123123', 'qwerty, qwerty qwer, 44-311', 137.00, '2024-09-27 06:56:21'),
(6, 'qwertyui', 'qwerty', '123456789', 'Wodzislaw slaski, qwerty 12, 44-311', 37.00, '2024-09-27 07:06:14'),
(7, 'nono', 'nono', '788909890', '09090, o9089 jkjk, 44-311', 37.00, '2024-09-27 10:10:43'),
(8, 'pitor', 'qwerty', '234567890', 'Wodzislaw slaski, qwerty 12, 44-311', 37.00, '2024-09-27 10:11:09'),
(9, 'pitor', 'qwerty', '234567899', 'qwerty, qwerty qwer, 44-311', 37.00, '2024-09-27 10:13:06'),
(10, 'pitor', 'qwerf', '123456789', '09090, qwerty 12, 44-311', 37.00, '2024-09-27 10:15:53'),
(11, 'pitor', 'qwerty', '234567890', 'Wodzislaw slaski, qwerty 12, 44-311', 37.00, '2024-09-27 10:16:45'),
(12, 'qwertyui', 'qwerty', '234567899', 'Wodzislaw slaski, qwerty 12, 44-311', 37.00, '2024-09-27 10:25:02'),
(14, 'qwert', 'qwert', '123456789', '123, qwer 2, 44-311', 142.00, '2024-09-30 09:18:12');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia_napoj`
--

CREATE TABLE `zamowienia_napoj` (
  `id` int(11) NOT NULL,
  `zamowienie_id` int(11) DEFAULT NULL,
  `napoj_id` int(11) DEFAULT NULL,
  `ilosc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zamowienia_napoj`
--

INSERT INTO `zamowienia_napoj` (`id`, `zamowienie_id`, `napoj_id`, `ilosc`) VALUES
(1, 1, 1, 1),
(2, 2, 3, 1),
(3, 3, 1, 1),
(4, 4, 1, 1),
(5, 5, 1, 1),
(6, 6, 1, 1),
(7, 7, 1, 1),
(8, 8, 1, 1),
(9, 9, 1, 1),
(10, 10, 1, 1),
(11, 11, 1, 1),
(12, 12, 1, 1),
(13, 14, 6, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia_pizza`
--

CREATE TABLE `zamowienia_pizza` (
  `id` int(11) NOT NULL,
  `zamowienie_id` int(11) DEFAULT NULL,
  `pizza_id` int(11) DEFAULT NULL,
  `ilosc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zamowienia_pizza`
--

INSERT INTO `zamowienia_pizza` (`id`, `zamowienie_id`, `pizza_id`, `ilosc`) VALUES
(1, 1, 8, 1),
(2, 1, 19, 1),
(3, 2, 1, 1),
(4, 2, 16, 2),
(5, 3, 1, 1),
(6, 4, 1, 1),
(7, 5, 2, 4),
(8, 5, 1, 1),
(9, 6, 1, 1),
(10, 7, 1, 1),
(11, 8, 1, 1),
(12, 9, 1, 1),
(13, 10, 1, 1),
(14, 11, 1, 1),
(15, 12, 1, 1),
(16, 14, 2, 3),
(17, 14, 3, 1),
(18, 14, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia_sos`
--

CREATE TABLE `zamowienia_sos` (
  `id` int(11) NOT NULL,
  `zamowienie_id` int(11) DEFAULT NULL,
  `sos_id` int(11) DEFAULT NULL,
  `ilosc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zamowienia_sos`
--

INSERT INTO `zamowienia_sos` (`id`, `zamowienie_id`, `sos_id`, `ilosc`) VALUES
(1, 1, 2, 1),
(2, 1, 1, 1),
(3, 2, 1, 1),
(4, 2, 5, 2),
(5, 3, 1, 1),
(6, 4, 1, 1),
(7, 5, 1, 1),
(8, 6, 1, 1),
(9, 7, 1, 1),
(10, 8, 1, 1),
(11, 9, 1, 1),
(12, 10, 1, 1),
(13, 11, 1, 1),
(14, 12, 1, 1),
(15, 14, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia_szczegoly`
--

CREATE TABLE `zamowienia_szczegoly` (
  `id` int(11) NOT NULL,
  `id_zamowienia` int(11) DEFAULT NULL,
  `id_pizzy` int(11) DEFAULT NULL,
  `ilosc_pizzy` int(11) DEFAULT NULL,
  `id_napoju` int(11) DEFAULT NULL,
  `ilosc_napojow` int(11) DEFAULT NULL,
  `id_sosu` int(11) DEFAULT NULL,
  `ilosc_sosu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `kontakt`
--
ALTER TABLE `kontakt`
  ADD PRIMARY KEY (`id_kontaktu`);

--
-- Indeksy dla tabeli `napoj`
--
ALTER TABLE `napoj`
  ADD PRIMARY KEY (`id_napoju`);

--
-- Indeksy dla tabeli `pizza`
--
ALTER TABLE `pizza`
  ADD PRIMARY KEY (`id_pizzy`);

--
-- Indeksy dla tabeli `sos`
--
ALTER TABLE `sos`
  ADD PRIMARY KEY (`id_sosu`);

--
-- Indeksy dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD PRIMARY KEY (`id_zamowienia`);

--
-- Indeksy dla tabeli `zamowienia_napoj`
--
ALTER TABLE `zamowienia_napoj`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zamowienie_id` (`zamowienie_id`),
  ADD KEY `napoj_id` (`napoj_id`);

--
-- Indeksy dla tabeli `zamowienia_pizza`
--
ALTER TABLE `zamowienia_pizza`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pizza` (`pizza_id`),
  ADD KEY `fk_zamowienie` (`zamowienie_id`);

--
-- Indeksy dla tabeli `zamowienia_sos`
--
ALTER TABLE `zamowienia_sos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zamowienie_id` (`zamowienie_id`),
  ADD KEY `sos_id` (`sos_id`);

--
-- Indeksy dla tabeli `zamowienia_szczegoly`
--
ALTER TABLE `zamowienia_szczegoly`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_zamowienia` (`id_zamowienia`),
  ADD KEY `id_pizzy` (`id_pizzy`),
  ADD KEY `id_napoju` (`id_napoju`),
  ADD KEY `id_sosu` (`id_sosu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kontakt`
--
ALTER TABLE `kontakt`
  MODIFY `id_kontaktu` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `napoj`
--
ALTER TABLE `napoj`
  MODIFY `id_napoju` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sos`
--
ALTER TABLE `sos`
  MODIFY `id_sosu` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `zamowienia`
--
ALTER TABLE `zamowienia`
  MODIFY `id_zamowienia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `zamowienia_napoj`
--
ALTER TABLE `zamowienia_napoj`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `zamowienia_pizza`
--
ALTER TABLE `zamowienia_pizza`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `zamowienia_sos`
--
ALTER TABLE `zamowienia_sos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `zamowienia_szczegoly`
--
ALTER TABLE `zamowienia_szczegoly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `zamowienia_napoj`
--
ALTER TABLE `zamowienia_napoj`
  ADD CONSTRAINT `zamowienia_napoj_ibfk_1` FOREIGN KEY (`zamowienie_id`) REFERENCES `zamowienia` (`id_zamowienia`),
  ADD CONSTRAINT `zamowienia_napoj_ibfk_2` FOREIGN KEY (`napoj_id`) REFERENCES `napoj` (`id_napoju`);

--
-- Constraints for table `zamowienia_pizza`
--
ALTER TABLE `zamowienia_pizza`
  ADD CONSTRAINT `fk_pizza` FOREIGN KEY (`pizza_id`) REFERENCES `pizza` (`id_pizzy`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_zamowienie` FOREIGN KEY (`zamowienie_id`) REFERENCES `zamowienia` (`id_zamowienia`) ON DELETE CASCADE;

--
-- Constraints for table `zamowienia_sos`
--
ALTER TABLE `zamowienia_sos`
  ADD CONSTRAINT `zamowienia_sos_ibfk_1` FOREIGN KEY (`zamowienie_id`) REFERENCES `zamowienia` (`id_zamowienia`),
  ADD CONSTRAINT `zamowienia_sos_ibfk_2` FOREIGN KEY (`sos_id`) REFERENCES `sos` (`id_sosu`);

--
-- Constraints for table `zamowienia_szczegoly`
--
ALTER TABLE `zamowienia_szczegoly`
  ADD CONSTRAINT `zamowienia_szczegoly_ibfk_1` FOREIGN KEY (`id_zamowienia`) REFERENCES `zamowienia` (`id_zamowienia`),
  ADD CONSTRAINT `zamowienia_szczegoly_ibfk_2` FOREIGN KEY (`id_pizzy`) REFERENCES `pizza` (`id_pizzy`),
  ADD CONSTRAINT `zamowienia_szczegoly_ibfk_3` FOREIGN KEY (`id_napoju`) REFERENCES `napoj` (`id_napoju`),
  ADD CONSTRAINT `zamowienia_szczegoly_ibfk_4` FOREIGN KEY (`id_sosu`) REFERENCES `sos` (`id_sosu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
