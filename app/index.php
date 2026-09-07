<?php
// Database configuration from environment variables
$host = getenv('DB_HOST') ?: 'mysql-service';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'password';
$database = getenv('DB_NAME') ?: 'guestbook';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle new entry submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO entries (message) VALUES (:message)");
        $stmt->execute([':message' => $message]);
        header('Location: /');
        exit;
    }
}

// Fetch all entries
$stmt = $pdo->query("SELECT * FROM entries ORDER BY created_at DESC LIMIT 100");
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Guestbook</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        input[type="text"] { width: 70%; padding: 10px; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        ul { list-style: none; padding: 0; }
        li { border-bottom: 1px solid #ddd; padding: 10px 0; }
        .timestamp { color: #666; font-size: 0.8em; }
        .health { display: inline-block; padding: 5px 10px; border-radius: 4px; color: white; }
        .healthy { background: green; }
        .unhealthy { background: red; }
    </style>
</head>
<body>
    <h1>📖 Guestbook</h1>
    
    <?php
    // Check database health
    try {
        $pdo->query("SELECT 1");
        echo '<span class="health healthy">✓ Database Connected</span>';
    } catch (Exception $e) {
        echo '<span class="health unhealthy">✗ Database Error</span>';
    }
    ?>
    
    <form method="POST" action="/">
        <h3>Sign the Guestbook</h3>
        <input type="text" name="message" placeholder="Write your message..." required>
        <button type="submit">Sign</button>
    </form>
    
    <h2>Recent Entries</h2>
    <?php if (count($entries) > 0): ?>
        <ul>
            <?php foreach ($entries as $entry): ?>
                <li>
                    <div><?php echo htmlspecialchars($entry['message']); ?></div>
                    <div class="timestamp"><?php echo $entry['created_at']; ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No entries yet. Be the first to sign!</p>
    <?php endif; ?>
</body>
</html>
