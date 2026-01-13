<?php
$env = parse_ini_file(__DIR__ . '/../.env');

// mysql connection (Aiven)
try {
    $pdo = new PDO(
        "mysql:host={$env['MYSQL_HOST']};port={$env['MYSQL_PORT']};dbname={$env['MYSQL_DB']}", 
        $env['MYSQL_USER'], 
        $env['MYSQL_PASS']
    );
} catch (PDOException $e) {
    die("MySQL Error: " . $e->getMessage());
}

// mongo db connection
try {
   
    $mongo = new MongoDB\Driver\Manager($env['MONGO_URI'], ['tlsInsecure' => true]);
} catch (Exception $e) {
    die("MongoDB Error: " . $e->getMessage());
}

//  Upstash (redis)connection
try {
    $redis = new Redis();
    
    // for deployment and localhosting
    $isWin = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $ca = $isWin ? 'C:\php\cacert.pem' : '/etc/ssl/certs/ca-certificates.crt';

    $options = [
        'stream' => [
            'cafile' => $ca,
            'verify_peer' => true,
        ]
    ];

    $redis->connect('tls://' . $env['REDIS_HOST'], (int)$env['REDIS_PORT'], 2.5, null, 0, 0, $options);
    $redis->auth($env['REDIS_PASS']);
} catch (Exception $e) {
    // error
    error_log("Redis Error: " . $e->getMessage());
}
?>