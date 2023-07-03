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

// Pobierz listę użytkowników
$usersQuery = "SELECT username FROM users WHERE username != '$username'";
$usersResult = mysqli_query($conn, $usersQuery);
$users = mysqli_fetch_all($usersResult, MYSQLI_ASSOC);

if (isset($_POST['transfer'])) {
    $amount = $_POST['amount'];
    $receiver = $_POST['receiver'];

    if ($amount <= $balance) {
        // Wykonaj przelew
        $newBalance = $balance - $amount;
        $updateQuery = "UPDATE users SET balance = $newBalance WHERE username = '$username'";
        mysqli_query($conn, $updateQuery);

        // Pobierz saldo odbiorcy
        $receiverQuery = "SELECT balance FROM users WHERE username = '$receiver'";
        $receiverResult = mysqli_query($conn, $receiverQuery);
        $receiverRow = mysqli_fetch_assoc($receiverResult);
        $receiverBalance = $receiverRow['balance'];

        $receiverBalance += $amount;
        $updateReceiverQuery = "UPDATE users SET balance = $receiverBalance WHERE username = '$receiver'";
        mysqli_query($conn, $updateReceiverQuery);

        // Dodaj przelew do historii transakcji
        $addTransactionQuery = "INSERT INTO transactions (sender, receiver, amount, date) VALUES ('$username', '$receiver', $amount, NOW())";
        mysqli_query($conn, $addTransactionQuery);

        $balance = $newBalance;
        //echo "Przelew wykonany pomyślnie.";
    } else {
    ///  echo "Nie masz wystarczających środków na koncie";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Strona banku</title>
    <link rel="icon" type="image/png" href="../icon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <style>
        a{font-size: 1em;}
        .logo {
            width: 200px;
            height: 200px;
            background-image: url("logo.PNG");
            background-size: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        input[type="number"] {
            padding: 8px 12px;
            border: 2px solid #478ac9;
            background-color: transparent;
            color: #478ac9;
            margin-bottom: 10px;
            border-radius: 50px;
            width: 200px;
            font-family: 'Roboto', sans-serif;
            font-size: 1em;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: transparent;
            color: #478ac9;
            border: 2px solid #478ac9;
            cursor: pointer;
            border-radius: 50px;
            transition: background-color 0.3s ease;
            font-family: 'Roboto', sans-serif;
            margin-bottom: 10px;
            font-size: 1em;

        }

        input[type="submit"]:hover {
            background-color: #478ac9;
            color: white;
            font-size: 1em;
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
            font-size: 1em;
        }

        .button:hover {
            background-color: #478ac9;
            color: white;
            font-size: 1em;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: transparent;
            color: #478ac9;
            text-decoration: none;
            border: 2px solid #478ac9;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border-radius: 50px;
            margin-top: 10px;
            font-family: 'Roboto', sans-serif;
            font-size: 1em;
        }

        .back-button:hover {
            background-color: #478ac9;
            color: white;
            font-size: 1em;
        }
        select {
    padding: 8px 12px;
    border: 2px solid #478ac9;
    background-color: transparent;
    color: #333;
    margin-bottom: 10px;
    border-radius: 50px;
    width: 200px;
    font-family: 'Roboto', sans-serif;
    font-size: 1em;
}
input[type="text"],
input[type="password"],
select {
    padding: 8px 12px;
    border: 2px solid #478ac9;
    background-color: transparent;
    color: #478ac9;
    margin-bottom: 10px;
    border-radius: 50px;
    width: 200px;
    font-family: 'Roboto', sans-serif;
    font-size: 1em;
}
.error-message {
            font-family: 'Roboto', sans-serif;
            color: #D9D9D9;
            margin-top: 10px;
            font-size: 1em;
        }
        .success-message {
            font-family: 'Roboto', sans-serif;
            color: #D9D9D9;
            margin-top: 10px;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <section class="section-1">
        
        <div class="container">
            <h4>Strona banku</h4>
            <img src="logo.PNG" alt="Logo" class="logo">
            <form method="POST" action="bank.php">
                <input type="number" name="amount" placeholder="Kwota przelewu" required><br>
                <select name="receiver">
                    <?php foreach ($users as $user) { ?>
                        <option value="<?php echo $user['username']; ?>"><?php echo $user['username']; ?></option>
                    <?php } ?>
                </select><br>
                <?php
                if (isset($_POST['transfer']) && $amount > $balance) {
                    echo "<p class='error-message'>Nie masz wystarczających środków na koncie.</p>";
                } elseif (isset($_POST['transfer'])) {
                    echo "<p class='success-message'>Przelew wykonany pomyślnie.</p>";
                }
                ?>
                <input type="submit" name="transfer" value="Wykonaj przelew" class="back-button">
            </form>
            <a href="home.php" class="back-button">Powrót</a>
        </div>
    </section>
</body>
</html>
