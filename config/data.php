<?php
function loadEnvironmentFile(string $path): void
{
  if (!is_readable($path)) {
    return;
  }

  foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
      continue;
    }

    [$name, $value] = explode('=', $line, 2);
    $name = trim($name);
    $value = trim($value);
    $value = trim($value, "\"'");

    if ($name !== '' && getenv($name) === false) {
      putenv($name . '=' . $value);
    }
  }
}

loadEnvironmentFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

$servername = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME') ?: 'register';

try {
  $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  error_log("Database connection failed: " . $e->getMessage());
  die("Database connection error. Please try again later.");
}
?>