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

// Pobierz historię przelewów użytkownika
$historyQuery = "SELECT * FROM transactions WHERE sender = '$username' OR receiver = '$username' ORDER BY id DESC";
$historyResult = mysqli_query($conn, $historyQuery);
$transactions = mysqli_fetch_all($historyResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Historia przelewów</title>
    <link rel="icon" type="image/png" href="../icon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia przelewów</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .logo {
            width: 200px;
            height: 200px;
            background-image: url("logo.PNG");
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            filter:alpha(opacity=85);
    opacity: 0.85;
    -moz-opacity:0.85;
        }

        th {
            background-color: #478ac9;
            color: white;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
        }

        

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .button {
            display: inline-block;
            padding: 8px 12px;
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
        td {
            background-color: gray;
            filter: alpha(opacity=85);
            opacity: 0.85;
            -moz-opacity:0.85;
        }
    </style>
    <meta http-equiv="refresh" content="15" > 
</head>
<body>
    <section class="section-1">
        <div class="container">
        <img src="logo.PNG" alt="Logo" class="logo">
            <h4>Historia przelewów dla użytkownika: <?php echo $username; ?></h4>
            <a href="home.php" class="button">Powrót</a>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Nadawca</th>
                    <th>Odbiorca</th>
                    <th>Kwota</th>
                    <th>Data</th>
                </tr>
                <?php foreach ($transactions as $transaction) : ?>
                    <tr>
                        <td><?php echo $transaction['id']; ?></td>
                        <td><?php echo $transaction['sender']; ?></td>
                        <td><?php echo $transaction['receiver']; ?></td>
                        <td><?php echo $transaction['amount']; ?></td>
                        <td><?php echo $transaction['date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <a href="home.php" class="button">Powrót</a>
        </div>
    </section>
</body>
</html>
