<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content'])) {
        savePost($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content']);
    } elseif (isset($_POST['comment_nick'], $_POST['comment_content'], $_POST['post_index'])) {
        addComment($_POST['post_index'], $_POST['comment_nick'], $_POST['comment_content']);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uwaga Klient!</title>
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

        document.addEventListener('DOMContentLoaded', () => {
            const posts = document.querySelectorAll('.post');

            posts.forEach(post => {
                const upvoteButton = post.querySelector('.upvote');
                const downvoteButton = post.querySelector('.downvote');
                const scoreElement = post.querySelector('.score');

                upvoteButton.addEventListener('click', () => {
                    updateScore(post.dataset.index, 1);
                });

                downvoteButton.addEventListener('click', () => {
                    updateScore(post.dataset.index, -1);
                });
            });
        });

        function updateScore(postIndex, value) {
            fetch('rate_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ postIndex: postIndex, value: value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const post = document.querySelector(`.post[data-index='${postIndex}']`);
                    const scoreElement = post.querySelector('.score');
                    scoreElement.textContent = data.newScore;
                } else {
                    alert('Błąd przy aktualizacji oceny');
                }
            });
        }
    </script>
</head>
<body>
    <header>
        <h1>Uwaga Klient!</h1>
    </header>
    <nav>
        <ul>
            <li><a href="#">Posty</a></li>
            <li><a href="#">O nas</a></li>
            <li><a href="#">Usługi</a></li>
            <li><a href="#">Kontakt</a></li>
        </ul>
    </nav>
    <main>
        <section class="posts">
            <h2>Posty</h2>
            <?php
            include_once 'functions.php';
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content'])) {
                savePost($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content']);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_nick'], $_POST['comment_content'], $_POST['post_index'])) {
                addComment($_POST['post_index'], $_POST['comment_nick'], $_POST['comment_content']);
            }
            displayPosts();
            ?>
        </section>
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
    </main>
    <footer>
        <p>&copy; Uwaga Klient. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
