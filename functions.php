<?php
function savePost($nick, $location, $title, $content, $shop, $customer_features, $image = null) {
    $post = array(
        'nick' => htmlspecialchars($nick),
        'location' => htmlspecialchars($location),
        'title' => htmlspecialchars($title),
        'content' => htmlspecialchars($content),
        'shop' => htmlspecialchars($shop),
        'customer_features' => htmlspecialchars($customer_features),
        'date' => date('Y-m-d H:i:s'),
        'comments' => array(),
        'score' => 0,
        'image' => $image 
    );
    $posts = loadPosts();
    $posts[] = $post;
    file_put_contents('posts.txt', serialize($posts));
}

function saveImage($imageFile) {
    $targetDir = "uploads/";
    // Create the uploads directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $targetFile = $targetDir . basename($imageFile["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($imageFile["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($imageFile["tmp_name"], $targetFile)) {
            return $targetFile;
        }
    }
    return null;
}

function loadPosts() {
    if (file_exists('posts.txt')) {
        $posts = unserialize(file_get_contents('posts.txt'));
        if ($posts !== false) {
            usort($posts, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            foreach ($posts as &$post) {
                if (!isset($post['comments']) || !is_array($post['comments'])) {
                    $post['comments'] = array();
                }
            }

            return $posts;
        }
    }
    return array();
}

function displayPosts($posts, $page = 1, $postsPerPage = 10) {
    $totalPosts = count($posts);
    $totalPages = ceil($totalPosts / $postsPerPage);
    $start = ($page - 1) * $postsPerPage;
    $end = min($start + $postsPerPage, $totalPosts);

    for ($i = $start; $i < $end; $i++) {
        $post = $posts[$i];
        echo '<div class="post" data-index="' . $i . '">';
        echo '<h3>' . $post['title'] . '</h3>';
        echo '<p><strong>Nick:</strong> ' . $post['nick'] . '</p>';
        echo '<p><strong>Lokalizacja:</strong> ' . $post['location'] . '</p>';
        echo '<p><strong>Sklep:</strong> ' . $post['shop'] . '</p>';
        echo '<p><strong>Cechy szczegółowe klienta:</strong> ' . nl2br($post['customer_features']) . '</p>';
        echo '<p><strong>Data:</strong> ' . $post['date'] . '</p>';
        echo '<p>' . nl2br($post['content']) . '</p>';
        if ($post['image']) {
            echo '<p><button onclick="toggleImage(' . $i . ')">Zobacz zdjęcie</button></p>';
            echo '<div id="image-' . $i . '" class="post-image"><img src="' . htmlspecialchars($post['image']) . '" alt="Zdjęcie załączone do posta"></div>';
        }
        echo '<div class="comments">';
        echo '<h4>Komentarze:</h4>';
        // Sprawdzenie czy `comments` jest tablicą
        if (isset($post['comments']) && is_array($post['comments'])) {
            foreach ($post['comments'] as $comment) {
                echo '<div class="comment">';
                echo '<p><strong>' . $comment['nick'] . ':</strong> ' . $comment['content'] . '</p>';
                echo '</div>';
            }
        }
        echo '<button class="comment-button" onclick="toggleCommentForm(' . $i . ')">Skomentuj</button>';
        echo '<div id="comment-form-' . $i . '" class="comment-form">';
        echo '<form action="index.php" method="POST">';
        echo '<input type="hidden" name="post_index" value="' . $i . '">';
        echo '<label for="comment_nick">Nick:</label>';
        echo '<input type="text" id="comment_nick" name="comment_nick" required>';
        echo '<label for="comment_content">Treść:</label>';
        echo '<textarea id="comment_content" name="comment_content" required></textarea>';
        echo '<input type="submit" value="Dodaj komentarz">';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '<div class="rating">';
        echo '<img src="upvote.png" alt="Upvote" class="upvote">';
        echo '<span class="score">' . $post['score'] . '</span>';
        echo '<img src="downvote.png" alt="Downvote" class="downvote">';
        echo '</div>';
        echo '</div>';
    }

    echo '<div class="pagination">';
    if ($page > 1) {
        echo '<a href="?page=' . ($page - 1) . '">&laquo; Poprzednia</a>';
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $page) {
            echo '<span class="current-page">' . $i . '</span>';
        } else {
            echo '<a href="?page=' . $i . '">' . $i . '</a>';
        }
    }
    if ($page < $totalPages) {
        echo '<a href="?page=' . ($page + 1) . '">Następna &raquo;</a>';
    }
    echo '</div>';
}

function addComment($postIndex, $nick, $content) {
    $posts = loadPosts();
    $comment = array(
        'nick' => htmlspecialchars($nick),
        'content' => htmlspecialchars($content)
    );
    $posts[$postIndex]['comments'][] = $comment;
    file_put_contents('posts.txt', serialize($posts));
}

function updateScore($postIndex, $value) {
    $posts = loadPosts();
    $posts[$postIndex]['score'] += $value;
    file_put_contents('posts.txt', serialize($posts));
}

function loadCities() {
    $cities = file('cities.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $cities;
}

function searchPostsByLocation($location) {
    $posts = loadPosts();
    $filteredPosts = array_filter($posts, function($post) use ($location) {
        return stripos($post['location'], $location) !== false;
    });
    return $filteredPosts;
}
?>