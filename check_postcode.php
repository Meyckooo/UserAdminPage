<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['post_code'])) {
    echo json_encode([
        'status'  => 'invalid',
        'message' => 'Invalid request.'
    ]);
    exit();
}

$post_code = trim($_POST['post_code']);
$user_id   = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

if (!preg_match('/^\d{4}$/', $post_code)) {
    echo json_encode([
        'status'  => 'invalid',
        'message' => 'Post code must be exactly 4 digits.'
    ]);
    exit();
}

if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database connection failed.'
    ]);
    exit();
}

// Kon sa edit screen (naay user_id), dili i-count ang kasamtangang user
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT oUserid FROM users WHERE post_code = ? AND oUserid != ?");
    $stmt->bind_param("si", $post_code, $user_id);
} else {
    // Sa add user screen
    $stmt = $conn->prepare("SELECT oUserid FROM users WHERE post_code = ?");
    $stmt->bind_param("s", $post_code);
}

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to prepare query.'
    ]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$exists = ($result && $result->num_rows > 0);

$stmt->close();

echo json_encode([
    'status' => $exists ? 'exists' : 'available'
]);
exit();