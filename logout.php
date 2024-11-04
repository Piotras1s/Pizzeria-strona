<?php
session_start();
session_destroy();  // Usunięcie wszystkich danych sesji
header("Location: login.php");  // Przekierowanie na stronę logowania po wylogowaniu
exit();
?>
