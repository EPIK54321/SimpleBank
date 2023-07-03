<?php
session_start();

// Sprawdzenie, czy użytkownik jest już zalogowany
if (isset($_SESSION['username'])) {
    header("Location: bank.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Strona główna</title>
    <link rel="icon" type="image/png" href="../icon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .logo {
    width: 200px;
    height: 200px;
    background-image: url("logo.png");
    background-size: cover;
    border-radius: 50%;
    margin-bottom: 20px;
}

        </style>
</head>
<body>
    <section class="section-1">
        <div class="container">
            <h4>Witaj na stronie banku</h4>
            <img src="logo.PNG" alt="Logo" class="logo">
            <div class="buttons">
                <a href="login.php" class="button">Zaloguj się</a>
                <a href="register.php" class="button">Zarejestruj się</a>
                <a href="support/login.php" class="button">Panel admina</a>
                <a href="../index.html" class="button">Wróć do strony głównej</a>
            </div>
        </div>
    </section>
</body>
</html>
