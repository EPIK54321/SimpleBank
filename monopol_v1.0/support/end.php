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

// Obsługa formularza usuwania użytkowników
if (isset($_POST['delete_users'])) {
    // Usuwanie wszystkich użytkowników
    $deleteUsersQuery = "DELETE FROM users";
    mysqli_query($conn, $deleteUsersQuery);

    // Usuwanie historii transakcji
    $deleteTransactionsQuery = "DELETE FROM transactions";
    mysqli_query($conn, $deleteTransactionsQuery);

    // Przekierowanie na stronę support.php po usunięciu użytkowników i historii transakcji
    header("Location: support.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Strona supportu</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 400px;
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #478ac9;
            text-align: center;
        }

        h3 {
            margin-top: 30px;
            margin-bottom: 10px;
            color: #666;
        }

        p {
            color: #666;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #478ac9;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #276592;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Witaj, <?php echo $admin; ?>!</h2>

        <!-- Formularz usuwania użytkowników i historii transakcji -->
        <h3>Usuwanie użytkowników i historii transakcji</h3>
        <p>Uwaga: Ta operacja usunie wszystkich użytkowników, ich saldo oraz historię transakcji. Czy na pewno chcesz kontynuować?</p>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="submit" name="delete_users" value="Usuń użytkowników i historię transakcji">
        </form>

        <!-- Formularz wylogowania -->
        <form method="POST" action="support.php">
            <input type="submit" name="back" value="Powrót">
        </form>
    </div>
</body>
</html>
