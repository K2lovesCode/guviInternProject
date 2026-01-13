<?php
require 'db.php';



//  validate session
$uid = $redis->get($_REQUEST['token'] ?? '');
if (!$uid) die(json_encode(['status' => 'unauthorized']));


$ns = "{$env['MONGO_DB']}.{$env['MONGO_COLLECTION']}";

//  handle logic
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = new MongoDB\Driver\Query(['user_id' => (int)$uid]);
    $res = $mongo->executeQuery($ns, $q)->toArray();
    echo json_encode($res[0] ?? ['age' => '', 'dob' => '', 'contact' => '']);
} else {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['user_id' => (int)$uid],
        ['$set' => ['age' => $_POST['age'], 'dob' => $_POST['dob'], 'contact' => $_POST['contact']]],
        ['upsert' => true]
    );
    $mongo->executeBulkWrite($ns, $bulk);
    echo json_encode(['status' => 'success']);
}