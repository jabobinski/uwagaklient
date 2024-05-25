<?php
function savePost($nick, $location, $title, $content) {
    $post = array(
        'nick' => htmlspecialchars($nick),
        'location' => htmlspecialchars($location),
        'title' => htmlspecialchars($title),
        'content' => htmlspecialchars($content),
        'comments' => array()
    );
    $posts = loadPosts();
    $posts[] = $post;
    file_put_contents('posts.txt', serialize($posts));
}

function loadPosts() {
    if (file_exists('posts.txt')) {
        $posts = unserialize(file_get_contents('posts.txt'));
        if ($posts !== false) {
            return $posts;
        }
    }
    return array();
}

function displayPosts() {
    $posts = loadPosts();
    foreach ($posts as $index => $post) {
        echo '<div class="post">';
        echo '<h3>' . $post['title'] . '</h3>';
        echo '<p><strong>Nick:</strong> ' . $post['nick'] . '</p>';
        echo '<p><strong>Lokalizacja:</strong> ' . $post['location'] . '</p>';
        echo '<p>' . nl2br($post['content']) . '</p>';
        echo '<div class="comments">';
        echo '<h4>Komentarze:</h4>';
        foreach ($post['comments'] as $comment) {
            echo '<div class="comment">';
            echo '<p><strong>' . $comment['nick'] . ':</strong> ' . $comment['content'] . '</p>';
            echo '</div>';
        }
        echo '<button class="comment-button" onclick="toggleCommentForm(' . $index . ')">Skomentuj</button>';
        echo '<div id="comment-form-' . $index . '" class="comment-form">';
        echo '<form action="index.php" method="POST">';
        echo '<input type="hidden" name="post_index" value="' . $index . '">';
        echo '<label for="comment_nick">Nick:</label>';
        echo '<input type="text" id="comment_nick" name="comment_nick" required>';
        echo '<label for="comment_content">Treść:</label>';
        echo '<textarea id="comment_content" name="comment_content" required></textarea>';
        echo '<input type="submit" value="Dodaj komentarz">';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
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
?>
