<?php
require_once 'config/config.php';

if (isset($_POST['post_code'])) {
    $post_code = trim($_POST['post_code']);
    $user_id   = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    // Kon sa edit screen (naay user_id), dili i-count ang kasamtangang user
    if ($user_id > 0) {
        $stmt = $conn->prepare("SELECT oUserid FROM users WHERE post_code = ? AND oUserid != ?");
        $stmt->bind_param("si", $post_code, $user_id);
    } else {
        // Sa add user screen
        $stmt = $conn->prepare("SELECT oUserid FROM users WHERE post_code = ?");
        $stmt->bind_param("s", $post_code);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'exists']);
    } else {
        echo json_encode(['status' => 'available']);
    }
    exit();
}
?>