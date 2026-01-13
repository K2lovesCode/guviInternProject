<?php


// load .env variables
if (!file_exists(__DIR__.'/../.env')) {
    die(json_encode(['status' => 'error', 'message' => '.env file missing']));
}

$env = [];
foreach (file(__DIR__.'/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if ($line[0] !== '#' && strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

// headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// mysql connection
try {
    $pdo = new PDO(
        "mysql:host={$env['MYSQL_HOST']};port={$env['MYSQL_PORT']};dbname={$env['MYSQL_DB']}", 
        $env['MYSQL_USER'], 
        $env['MYSQL_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]));
}

// mongodb connection
try {

    $mongoOptions = [
        'tlsInsecure' => true
    ];
    $mongo = new MongoDB\Driver\Manager($env['MONGO_URI'], $mongoOptions);
} catch (Exception $e) {
    die(json_encode(['status' => 'error', 'message' => 'NoSQL Error: ' . $e->getMessage()]));
}

// Redis Connection
try {
    $redis = new Redis(); 
    $host = $env['REDIS_HOST'];
    $port = (int)$env['REDIS_PORT'];

    if (isset($env['REDIS_TLS']) && $env['REDIS_TLS'] === 'true') {
        $host = 'tls://' . $host;
    }

    // connect
    if (!$redis->connect($host, $port)) {
        error_log("Redis connection failed to $host");
    }
    
    if (!empty($env['REDIS_PASS'])) {
        $redis->auth($env['REDIS_PASS']);
    }
} catch (Exception $e) {
    die(json_encode(['status' => 'error', 'message' => 'Redis Error: ' . $e->getMessage()]));
}
?>