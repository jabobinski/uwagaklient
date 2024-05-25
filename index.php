<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moja Strona</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Witamy na naszej stronie!</h1>
    </header>
    <nav>
        <ul>
            <li><a href="#">Strona Główna</a></li>
            <li><a href="#">O nas</a></li>
            <li><a href="#">Usługi</a></li>
            <li><a href="#">Kontakt</a></li>
        </ul>
    </nav>
    <main>
        <section class="post-form">
            <h2>Napisz Post</h2>
            <form action="index.php" method="POST">
                <label for="nick">Nick:</label>
                <input type="text" id="nick" name="nick" required>
                <label for="location">Lokalizacja:</label>
                <input type="text" id="location" name="location" required>
                <label for="title">Tytuł:</label>
                <input type="text" id="title" name="title" required>
                <label for="content">Treść:</label>
                <textarea id="content" name="content" required></textarea>
                <input type="submit" value="Opublikuj">
            </form>
        </section>
        <section class="posts">
            <h2>Posty</h2>
            <?php
            include 'functions.php';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                savePost($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content']);
            }
            displayPosts();
            ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Moja Strona. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>