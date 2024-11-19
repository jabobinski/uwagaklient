<?php
session_start();
include 'functions.php';

if (!isset($_COOKIE['user_declined'])) {
    header("Location: welcome.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content'], $_POST['shop'], $_POST['customer_features'])) {
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = saveImage($_FILES['image']);
        }
        savePost($_POST['nick'], $_POST['location'], $_POST['title'], $_POST['content'], $_POST['shop'], $_POST['customer_features'], $image);
    } elseif (isset($_POST['comment_nick'], $_POST['comment_content'], $_POST['post_index'])) {
        addComment($_POST['post_index'], $_POST['comment_nick'], $_POST['comment_content']);
    } elseif (isset($_POST['delete_post_index']) && isset($_SESSION['admin']) && $_SESSION['admin']) {
        deletePost($_POST['delete_post_index']);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$searchLocation = isset($_GET['search_location']) ? htmlspecialchars($_GET['search_location']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date';

if (!empty($searchLocation)) {
    $filteredPosts = searchPostsByLocation($searchLocation);
} else {
    $filteredPosts = loadPosts($sort);
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

        function showPostForm() {
            var modal = document.getElementById('post-modal');
            modal.style.display = 'block';
            setTimeout(() => {
                modal.classList.add('visible');
            }, 10);
        }

        function hidePostForm() {
            var modal = document.getElementById('post-modal');
            modal.classList.remove('visible');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        function sortPostsBy(criteria) {
            var form = document.getElementById('sort-form');
            form.sort.value = criteria;
            form.submit();
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
    <header>
        <h1>Uwaga Klient!</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Posty</a></li>
            <li><a href="about.php">O nas</a></li>
            <li><a href="services.php">Usługi</a></li>
            <li><a href="contact.php">Kontakt</a></li>
            <li><a href="requirements.php">Wymagania</a></li>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
                <li><a href="logout.php">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="login.php">Zaloguj</a></li>
            <?php endif; ?>
        </ul>
        <form id="search-form" action="index.php" method="GET">
            <label for="search_location" style="color:white">Szukaj po lokalizacji:</label>
            <input type="text" id="search_location" name="search_location" value="<?php echo htmlspecialchars($searchLocation); ?>" required>
            <input type="submit" value="Szukaj">
        </form>
        <div class="sort-buttons">
            <span style="color:white">Sortuj według:</span>
            <button type="button" class="sort-button" onclick="sortPostsBy('score')">Ocen</button>
            <button type="button" class="sort-button" onclick="sortPostsBy('date')">Najnowszych</button>
        </div>
        <form id="sort-form" action="index.php" method="GET" style="display: none;">
            <input type="hidden" name="search_location" value="<?php echo htmlspecialchars($searchLocation); ?>">
            <input type="hidden" name="sort" value="">
        </form>
    </nav>
    <main>
        <button class="open-post-form" onclick="showPostForm()">Napisz Post</button>
        <section class="posts">
            <?php
            if (!empty($filteredPosts)) {
                displayPosts($filteredPosts, $page);
            } else {
                echo '<p>Brak postów do wyświetlenia.</p>';
            }
            ?>
        </section>
        <section class="paginations">
            <?php
            if (!empty($filteredPosts)) {
                displayPagination($filteredPosts, $page);
            } else {
                echo '<p>Brak postów do wyświetlenia.</p>';
            }
            ?>
        </section>
    </main>
    <div id="post-modal" class="post-modal">
        <div class="post-modal-content">
            <span class="close" onclick="hidePostForm()">&times;</span>
            <h2>Napisz Post</h2>
            <form action="index.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nick">Nick:</label>
                    <input type="text" id="nick" name="nick" required>
                </div>
                <div class="form-group">
                    <label for="location">Lokalizacja:</label>
                    <input type="text" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="title">Tytuł:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="content">Treść:</label>
                    <textarea id="content" name="content" required></textarea>
                </div>
                <div class="form-group">
                    <label for="shop">Sklep:</label>
                    <input type="text" id="shop" name="shop" required>
                </div>
                <div class="form-group">
                    <label for="customer_features">Cecha klienta:</label>
                    <input type="text" id="customer_features" name="customer_features" required>
                </div>
                <div class="form-group">
                    <label for="image">Zdjęcie (opcjonalnie):</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <input type="submit" value="Dodaj Post" class="submit-btn">
            </form>
        </div>
    </div>
</body>
</html>
