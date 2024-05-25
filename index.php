<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moja Strona</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleCommentForm(postIndex) {
            var form = document.getElementById('comment-form-' + postIndex);
            if (form.style.maxHeight === '0px' || form.style.maxHeight === '') {
                form.style.maxHeight = form.scrollHeight + 'px';
            } else {
                form.style.maxHeight = '0px';
            }
        }
    </script>
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content'])) {
                savePost($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content']);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_nick'], $_POST['comment_content'], $_POST['post_index'])) {
                addComment($_POST['post_index'], $_POST['comment_nick'], $_POST['comment_content']);
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
