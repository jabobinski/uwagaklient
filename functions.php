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
    savePosts($posts);
}

function saveImage($imageFile) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $targetFile = $targetDir . basename($imageFile["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($imageFile["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($imageFile["tmp_name"], $targetFile)) {
            return $targetFile;
        }
    }
    return null;
}

function loadPosts($sort = 'date') {
    if (file_exists('posts.txt')) {
        $posts = unserialize(file_get_contents('posts.txt'));
        if ($posts !== false) {
            if ($sort == 'score') {
                usort($posts, function($a, $b) {
                    return $b['score'] - $a['score'];
                });
            } else {
                usort($posts, function($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });
            }
            // Reszta funkcji pozostaje bez zmian
            foreach ($posts as &$post) {
                if (!isset($post['nick'])) $post['nick'] = '';
                if (!isset($post['location'])) $post['location'] = '';
                if (!isset($post['title'])) $post['title'] = '';
                if (!isset($post['content'])) $post['content'] = '';
                if (!isset($post['shop'])) $post['shop'] = '';
                if (!isset($post['customer_features'])) $post['customer_features'] = '';
                if (!isset($post['date'])) $post['date'] = date('Y-m-d H:i:s');
                if (!isset($post['comments']) || !is_array($post['comments'])) {
                    $post['comments'] = array();
                }
                if (!isset($post['score'])) $post['score'] = 0;
                if (!isset($post['image'])) $post['image'] = null;
            }
            return $posts;
        }
    }
    return array();
}

function savePosts($posts) {
    $file = fopen('posts.txt', 'w');
    if (flock($file, LOCK_EX)) {  // acquire an exclusive lock
        fwrite($file, serialize($posts));
        flock($file, LOCK_UN);    // release the lock
    }
    fclose($file);
}

function displayPosts($posts, $page = 1, $postsPerPage = 6) {
    $totalPosts = count($posts);
    $totalPages = ceil($totalPosts / $postsPerPage);
    $start = ($page - 1) * $postsPerPage;
    $end = min($start + $postsPerPage, $totalPosts);

    usort($posts, function($a, $b) {
        global $sort;
        if ($sort == 'score') {
            return $b['score'] - $a['score'];
        } else { // Default to sort by date
            return strtotime($b['date']) - strtotime($a['date']);
        }
    });

    for ($i = $start; $i < $end; $i++) {
        $post = $posts[$i];
        echo '<div class="post" data-index="' . $i . '">';
        echo '<h3 class="postitle">' . htmlspecialchars($post['title']) . '</h3>';
        echo '<hr>'; // Biała linia oddzielająca tytuł od reszty posta
        
        // Blok kontenera dla podziału na lewy i prawy blok
        echo '<div class="post-container">';
        
        // Lewy blok
        echo '<div class="post-left">';
        echo '<div class="post-nick"><strong><span class="post-icon"></span>' . htmlspecialchars($post['nick']) . '</strong></div>';
        echo '<div class="post-content">' . nl2br(htmlspecialchars($post['content'])) . '</div>';
        echo '<div class="post-features"><strong>Cechy szczegółowe klienta:</strong> ' . nl2br(htmlspecialchars($post['customer_features'])) . '</div>';
        echo '<hr>';
        echo '</div>';
        
        // Prawy blok
        echo '<div class="post-right">';
        echo '<div class="post-date"><span class="post-icon"></span>' . htmlspecialchars($post['date']) . '</div>';
        echo '<div class="post-location"><span class="post-icon"></span>' . htmlspecialchars($post['location']) . '</div>';
        echo '<div class="post-shop"><span class="post-icon"></span>' . htmlspecialchars($post['shop']) . '</div>';
        echo '</div>';
        
        echo '</div>'; // Koniec kontenera
        
        if ($post['image']) {
            echo '<p><button class="comment-button" onclick="toggleImage(' . $i . ')">Zobacz zdjęcie</button></p>';
            echo '<div id="image-' . $i . '" class="post-image"><img src="' . htmlspecialchars($post['image']) . '" alt="Zdjęcie załączone do posta"></div>';
        }
        
        echo '<div class="comments">';
        echo '<h4>Komentarze:</h4>';
        if (isset($post['comments']) && is_array($post['comments'])) {
            foreach ($post['comments'] as $comment) {
                echo '<div class="comment">';
                echo '<p><strong>' . htmlspecialchars($comment['nick']) . ':</strong> ' . htmlspecialchars($comment['content']) . '</p>';
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
        echo '<textarea id="comment_content" name="comment_content" maxlength="100" required></textarea>';
        echo '<input type="submit" class="comment-button" value="Dodaj komentarz">';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '<div class="rating">';
        echo '<img src="upvote.png" alt="Upvote" class="upvote">';
        echo '<span class="score">' . htmlspecialchars($post['score']) . '</span>';
        echo '<img src="downvote.png" alt="Downvote" class="downvote">';
        if (isset($_SESSION['admin']) && $_SESSION['admin']) {
            echo '<form action="index.php" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="delete_post_index" value="' . $i . '">';
            echo '<input type="submit" class="delete-btn" value="Usuń">';
            echo '</form>';
        }
        echo '</div>';
        echo '</div>';


        echo '</div>';
    }
}
function displayPagination($posts, $page = 1, $postsPerPage = 6) {
    $totalPosts = count($posts);
    $totalPages = ceil($totalPosts / $postsPerPage);
    $start = ($page - 1) * $postsPerPage;
    $end = min($start + $postsPerPage, $totalPosts);

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
    if (strlen($content) > 100) {
        // Zwróć błąd, jeśli komentarz przekracza 250 znaków
        return false;
    }
    $posts = loadPosts();
    $comment = array(
        'nick' => htmlspecialchars($nick),
        'content' => htmlspecialchars($content)
    );
    if (isset($posts[$postIndex])) {
        $posts[$postIndex]['comments'][] = $comment;
        savePosts($posts);
    }
}

function updateScore($postIndex, $value) {
    $posts = loadPosts();
    if (isset($posts[$postIndex])) {
        $posts[$postIndex]['score'] += $value;
        savePosts($posts);
    }
}

function loadCities() {
    $cities = file('cities.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $cities;
}

function searchPostsByLocation($location) {
    $posts = loadPosts();
    $filteredPosts = array_filter($posts, function($post) use ($location) {
        return stripos($post['location'], $location) !== false && 
               !empty($post['title']) && 
               !empty($post['content']) &&
               !empty($post['nick']) &&
               !empty($post['shop']) &&
               !empty($post['customer_features']);
    });
    return $filteredPosts;
}

function deletePost($postIndex) {
    $posts = loadPosts();
    if (isset($posts[$postIndex])) {
        array_splice($posts, $postIndex, 1); // Usuwa post na podstawie indeksu
        savePosts($posts);
    }
}
?>