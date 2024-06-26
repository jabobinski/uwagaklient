<?php
include 'functions.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['postIndex']) && isset($data['value'])) {
    $postIndex = intval($data['postIndex']);
    $value = intval($data['value']);
    updateScore($postIndex, $value);

    $posts = loadPosts();
    $newScore = $posts[$postIndex]['score'];
    
    echo json_encode(['success' => true, 'newScore' => $newScore]);
} else {
    echo json_encode(['success' => false]);
}
?>