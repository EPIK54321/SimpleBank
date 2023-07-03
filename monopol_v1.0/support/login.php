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

if (isset($_SESSION['admin'])) {
    header("Location: support.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if ($password == $row['password']) {
            $_SESSION['admin'] = $username;
            header("Location: support.php");
            exit();
        } else {
            echo "Nieprawidłowe hasło.";
        }
    } else {
        echo "Nieprawidłowa nazwa użytkownika.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Strona logowania administratora</title>
    <link rel="icon" type="image/png" href="/icon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona logowania administratora</title>
    <link rel="stylesheet" href="style.css">
    <style>
     
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
            <h4>Logowanie administratora</h4>
            <img src="logo.PNG" alt="Logo" class="logo">
            <form method="POST" action="login.php">
                <input type="text" name="username" placeholder="Nazwa użytkownika" required><br>
                <input type="password" name="password" placeholder="Hasło" required><br>
                <?php
                    if (isset($_POST['login']) && mysqli_num_rows($result) != 1) {
                        echo "<p class='error-message'>Nieprawidłowe dane logowania.</p>";
                    }
                ?>
                <input type="submit" name="login" value="Zaloguj" class="back-button">
            </form>
            <a href="../index.php" class="back-button">Wróć do strony głównej</a>
        </div>
    </section>
</body>
</html>
