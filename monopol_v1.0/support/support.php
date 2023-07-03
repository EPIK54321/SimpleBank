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

// Sprawdzenie, czy administrator jest zalogowany
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Pobierz dane administratora z bazy danych
$admin = $_SESSION['admin'];
$query = "SELECT * FROM admins WHERE username = '$admin'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$adminId = $row['id'];

// Obsługa formularza ustawiania salda użytkownika
if (isset($_POST['set_balance'])) {
    $username = $_POST['username'];
    $balance = $_POST['balance'];

    // Sprawdzenie, czy użytkownik istnieje w bazie danych
    $checkUserQuery = "SELECT * FROM users WHERE username = '$username'";
    $checkUserResult = mysqli_query($conn, $checkUserQuery);

    if (mysqli_num_rows($checkUserResult) == 1) {
        // Aktualizacja salda użytkownika
        $updateBalanceQuery = "UPDATE users SET balance = $balance WHERE username = '$username'";
        mysqli_query($conn, $updateBalanceQuery);

        // Dodanie wpisu do historii transakcji użytkownika
        $transactionQuery = "INSERT INTO transactions (sender, receiver, amount, date) VALUES ('$admin', '$username', $balance, NOW())";
        mysqli_query($conn, $transactionQuery);

        // Przekierowanie na stronę support.php po przetworzeniu formularza
        header("Location: support.php");
        exit();
    } else {
        echo "Nieprawidłowa nazwa użytkownika.";
    }
}

// Obsługa formularza dodawania kwoty dla wszystkich użytkowników
if (isset($_POST['add_to_all'])) {
    $amount = $_POST['amount'];

    // Pobierz listę użytkowników
    $usersQuery = "SELECT username FROM users";
    $usersResult = mysqli_query($conn, $usersQuery);

    if ($usersResult) {
        while ($row = mysqli_fetch_assoc($usersResult)) {
            $username = $row['username'];
            addAmountToUser($amount, $username, $admin, $conn);
        }

        echo "Dodano kwotę $amount dla wszystkich użytkowników.";
    } else {
        echo "Wystąpił błąd podczas pobierania listy użytkowników.";
    }
}

// Funkcja dodająca kwotę dla pojedynczego użytkownika
function addAmountToUser($amount, $username, $admin, $conn) {
    // Pobranie bieżącego salda użytkownika
    $getUserQuery = "SELECT balance FROM users WHERE username = '$username'";
    $getUserResult = mysqli_query($conn, $getUserQuery);
    $userData = mysqli_fetch_assoc($getUserResult);
    $currentBalance = $userData['balance'];

    // Obliczenie nowego salda
    $newBalance = $currentBalance + $amount;

    // Aktualizacja salda użytkownika
    $updateQuery = "UPDATE users SET balance = $newBalance WHERE username = '$username'";
    mysqli_query($conn, $updateQuery);

    // Dodanie wpisu do historii transakcji użytkownika
    $transactionQuery = "INSERT INTO transactions (sender, receiver, amount, date) VALUES ('$admin', '$username', $amount, NOW())";
    mysqli_query($conn, $transactionQuery);
}

// Obsługa formularza końca gry/usunięcia kont
if (isset($_POST['end_game'])) {
    // Usunięcie wszystkich użytkowników i historii transakcji
    $deleteUsersQuery = "DELETE FROM users";
    $deleteTransactionsQuery = "DELETE FROM transactions";
    mysqli_query($conn, $deleteUsersQuery);
    mysqli_query($conn, $deleteTransactionsQuery);

    // Przekierowanie na stronę support.php po przetworzeniu formularza
    header("Location: support.php");
    exit();
}

// Pobierz listę użytkowników
$userQuery = "SELECT * FROM users";
$userResult = mysqli_query($conn, $userQuery);
$users = mysqli_fetch_all($userResult, MYSQLI_ASSOC);

// Pobierz listę transakcji
$transactionQuery = "SELECT * FROM transactions ORDER BY id DESC";
$transactionResult = mysqli_query($conn, $transactionQuery);
$transactions = mysqli_fetch_all($transactionResult, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Strona supportu</title>
    <link rel="icon" type="image/png" href="/icon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            color: #D9D9D9;
        }
        h3 {
            margin-top: 30px;
            margin-bottom: 10px;
            color: #478ac9;
        }
        form {
            margin-bottom: 20px;
        }
        select, input[type="number"] {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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

        td {
            background-color: gray;
            filter: alpha(opacity=85);
            opacity: 0.85;
            -moz-opacity:0.85;
            color: #26292b;
        }
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
        /* Responsywne style */
        @media screen and (max-width: 600px) {
            form, table {
                width: 100%;
            }
        }
    </style>
    <style>
        .section-1 {
            text-align: center;
            padding: 20px;
        }

        .logo {
            width: 200px;
            height: 200px;
            background-image: url("logo.PNG");
            background-size: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            padding: 8px 12px;
            border: 2px solid #478ac9;
            background-color: transparent;
            color: #478ac9;
            margin-bottom: 10px;
            border-radius: 50px;
            width: 200px;
            font-family: 'Roboto', sans-serif;
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
        }

        input[type="submit"]:hover {
            background-color: #478ac9;
            color: white;
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
            text-transform: uppercase;
        }

        .back-button:hover {
            background-color: #478ac9;
            color: white;
        }

        .error-message {
            font-family: 'Roboto', sans-serif;
            color: #D9D9D9;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <section class="section-1">
        <div class="container">
            <h2>Witaj, <?php echo $admin; ?>!</h2>

            <!-- Formularz wylogowania -->
            <form method="POST" action="logout.php">
                <input type="submit" name="logout" value="Wyloguj się" class="back-button">
            </form>

            <!-- Formularz ustawiania salda użytkownika -->
            <h3>Ustawianie salda użytkownika</h3>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <select name="username" required>
                    <option value="">Wybierz użytkownika</option>
                    <?php foreach ($users as $user) : ?>
                        <option value="<?php echo $user['username']; ?>"><?php echo $user['username']; ?></option>
                    <?php endforeach; ?>
                </select><br>
                <input type="number" name="balance" placeholder="Nowe saldo" required><br>
                <input type="submit" name="set_balance" value="Ustaw saldo" class="back-button">
            </form>

            <!-- Formularz dodawania kwoty dla wszystkich użytkowników -->
            <h3>Dodawanie kwoty dla wszystkich użytkowników</h3>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="number" name="amount" placeholder="Kwota" required><br>
                <input type="submit" name="add_to_all" value="Dodaj do wszystkich" class="back-button">
            </form>

            <!-- Przycisk "Koniec gry/Usunięcie kont" -->
            <h3>Koniec gry/Usunięcie kont</h3>
            <form method="POST" action="end.php">
                <input type="submit" name="Endgame" value="Koniec GRY" class="back-button">
            </form>

            <!-- Lista użytkowników -->
            <h3>Lista użytkowników</h3>
            <table>
                <tr>
                    <th>Nazwa użytkownika</th>
                    <th>Saldo</th>
                </tr>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['balance']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <!-- Historia transakcji -->
            <h3>Historia transakcji</h3>
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
            <br></br>
            <!-- Formularz wylogowania -->
            <form method="POST" action="logout.php">
                <input type="submit" name="logout" value="Wyloguj się" class="back-button">
            </form>
        </div>
    </section>
</body>
</html>
