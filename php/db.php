<?php

// load .env variables
$envFile = __DIR__.'/../.env';
$env = [];

if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line && $line[0] !== '#' && strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }
}

// helper
function getEnvVar($key, $data) {
    if (isset($data[$key])) return $data[$key];
    $val = getenv($key);
    return $val !== false ? $val : null;
}

// populate $env 
$required_vars = [
    'MYSQL_HOST', 'MYSQL_PORT', 'MYSQL_DB', 'MYSQL_USER', 'MYSQL_PASS',
    'MONGO_URI',
    'REDIS_HOST', 'REDIS_PORT', 'REDIS_PASS', 'REDIS_TLS'
];

foreach ($required_vars as $key) {
    if (!isset($env[$key])) {
        $val = getenv($key);
        if ($val !== false) {
            $env[$key] = $val;
        }
    }
}

// check for critical variables
if (empty($env['MYSQL_HOST'])) {
    
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