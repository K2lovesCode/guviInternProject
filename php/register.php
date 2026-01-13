<?php
require 'db.php';

// register logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // check for duplicate
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$user]);
    if ($stmt->fetch()) die(json_encode(['status' => 'error', 'message' => 'Username taken']));

    // insert mysql
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$user, password_hash($pass, PASSWORD_BCRYPT)])) {
        // insert mongo
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert(['user_id' => (int)$pdo->lastInsertId(), 'age' => '', 'dob' => '', 'contact' => '']);
        $mongo->executeBulkWrite("{$env['MONGO_DB']}.{$env['MONGO_COLLECTION']}", $bulk);
        
        echo json_encode(['status' => 'success']);
    }
}