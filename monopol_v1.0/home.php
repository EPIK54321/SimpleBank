<?php
session_start();

$host = "localhost";
$username = "patryk";
$password = "niedlapsa";
$database = "monopol";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT balance FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$balance = $row['balance'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Moja Strona</title>
    <link rel="icon" type="image/png" href="../icon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moja Strona</title>
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
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
           
        }

        h2 {
            margin-bottom: 20px;
            color: #D9D9D9;
            font-size: 1em;
        }

        .balance {
            font-size: 24px;
            margin-bottom: 20px;
            
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: transparent;
            color: #478ac9;
            text-decoration: none;
            border: 2px solid #478ac9;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border-radius: 50px;
            font-family: 'Roboto', sans-serif;
            margin-bottom: 10px;
            
        }

        .button:hover {
            background-color: #478ac9;
            color: white;
            
        }
    </style>
    <meta http-equiv="refresh" content="15" > 
</head>
<body>
    <section class="section-1">
        <div class="container">
        <img src="logo.PNG" alt="Logo" class="logo">
            <h4>Witaj na stronie</h4>
            <div class="balance">
                <h2>Bilans konta: <?php echo $balance; ?></h2>
            </div>
            <a class="button" href="bank.php">Wykonaj przelew</a>
            <a class="button" href="historia.php">Historia</a>
            <a class="button" href="logout.php">Wyloguj się</a>
        </div>
    </section>
</body>
</html>
