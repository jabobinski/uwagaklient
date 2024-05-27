<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content'], $_POST['shop'], $_POST['customer_features'])) {
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = saveImage($_FILES['image']);
        }
        savePost($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content'], $_POST['shop'], $_POST['customer_features'], $image);
    } elseif (isset($_POST['comment_nick'], $_POST['comment_content'], $_POST['post_index'])) {
        addComment($_POST['post_index'], $_POST['comment_nick'], $_POST['comment_content']);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if (isset($_GET['search_location'])) {
    $searchLocation = htmlspecialchars($_GET['search_location']);
    $filteredPosts = searchPostsByLocation($searchLocation);
} else {
    $filteredPosts = loadPosts();
}

$cities = loadCities();
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

        function toggleImage(postIndex) {
            var image = document.getElementById('image-' + postIndex);
            if (image.style.maxHeight === '0px' || image.style.maxHeight === '') {
                image.style.maxHeight = image.scrollHeight + 'px';
            } else {
                image.style.maxHeight = '0px';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var upvoteButtons = document.querySelectorAll('.upvote');
            var downvoteButtons = document.querySelectorAll('.downvote');
            
            upvoteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var postIndex = this.closest('.post').getAttribute('data-index');
                    Score(postIndex, 1);
                });
            });
            
            downvoteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var postIndex = this.closest('.post').getAttribute('data-index');
                    Score(postIndex, -1);
                });
            });
        });

        function Score(postIndex, value) {
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
        <form action="index.php" method="GET">
            <label for="search_location">Szukaj po lokalizacji:</label>
            <input type="text" id="search_location" name="search_location" required>
            <input type="submit" value="Szukaj">
        </form>
    </nav>
    <main>
        <section class="posts">
            <h2>Posty</h2>
            <?php
            displayPosts($filteredPosts, $page);
            ?>
        </section>
        <section class="post-form">
            <h2>Napisz Post</h2>
            <form action="index.php" method="POST" enctype="multipart/form-data">
                <label for="nick">Nick:</label>
                <input type="text" id="nick" name="nick" required>
                <label for="location">Lokalizacja:</label>
                <input list="cities" id="location" name="location" required>
                <datalist id="cities">
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo htmlspecialchars($city); ?>">
                    <?php endforeach; ?>
                </datalist>
                <label for="shop">Sklep:</label>
                <input type="text" id="shop" name="shop" required>
                <label for="customer_features">Cechy szczegółowe klienta:</label>
                <textarea id="customer_features" name="customer_features" required></textarea>
                <label for="title">Tytuł:</label>
                <input type="text" id="title" name="title" required>
                <label for="content">Treść:</label>
                <textarea id="content" name="content" required></textarea>
                <label for="image">Załącz zdjęcie:</label>
                <input type="file" id="image" name="image">
                <input type="submit" value="Opublikuj">
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; Uwaga Klient. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>