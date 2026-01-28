<?php

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Supabase database connection configuration
$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$database = $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

echo "===================================================\n";
echo "     Supabase Database Connection Test\n";
echo "===================================================\n\n";

echo "Connection details:\n";
echo "\tHost: " . $host . "\n";
echo "\tPort: " . $port . "\n";
echo "\tDatabase: " . $database . "\n";
echo "\tUsername: " . $username . "\n\n";

echo "Attempting to connect...\n";

try {
    // Create a new PDO instance for PostgreSQL
    $dsn = "pgsql:host={$host};port={$port};dbname={$database};sslmode=require";

    // PDO options
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ];

    // Establish the connection
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "Connection successful!\n\n";
    
    // Get server version
    $versionResult = $pdo->query("SELECT version();")->fetch(PDO::FETCH_ASSOC);
    echo "Server information:\n";
    echo $versionResult['version'] . "\n\n";
    
    // Get database size
    $sizeResult = $pdo->query(
        "SELECT pg_size_pretty(pg_database_size(current_database())) as size;"
    )->fetch(PDO::FETCH_ASSOC);
    echo "Database size: " . $sizeResult['size'] . "\n\n";
    
    // List all tables in public schema
    $tablesResult = $pdo->query(
        "SELECT table_name FROM information_schema.tables 
         WHERE table_schema = 'public' ORDER BY table_name;"
    )->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Tables in public schema (" . count($tablesResult) . "):\n";
    if (count($tablesResult) > 0) {
        foreach ($tablesResult as $table) {
            echo "-" . $table['table_name'] . "\n";
        }
    } else {
        echo "No tables found\n";
    }
    
    echo "\n===================================================\n";
    echo "Connection is working correctly!\n";
    echo "===================================================\n";
    
} catch (PDOException $e) {
    // Handle connection error
    echo "===================================================\n";
    echo "Failed to connect to the database.\n";
    echo "===================================================\n";
    echo "Connection error: " . $e->getMessage() . "\n\n";
    exit(1);
}
