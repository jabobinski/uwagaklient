<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Kontakt</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Posty</a></li>
            <li><a href="about.php">O nas</a></li>
            <li><a href="services.php">Usługi</a></li>
            <li><a href="contact.php">Kontakt</a></li>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
                <li><a href="logout.php">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="login.php">Zaloguj</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
        <span>Możesz się z nami skontaktować pod adresem email: super@mail.com</span>
    </main>
</body>
</html>