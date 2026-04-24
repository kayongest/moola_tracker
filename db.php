<?php
// db.php - Database connection and initialization

try {
    // Check if we are on Render and use the persistent disk path if available
    $dbPath = getenv('RENDER_DISK_PATH') ? getenv('RENDER_DISK_PATH') . '/moola.db' : __DIR__ . '/moola.db';
    
    // Ensure the directory exists for the disk
    if (getenv('RENDER_DISK_PATH') && !file_exists(getenv('RENDER_DISK_PATH'))) {
        mkdir(getenv('RENDER_DISK_PATH'), 0777, true);
    }

    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialize the table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS loans (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        borrower_name TEXT NOT NULL,
        lender_name TEXT DEFAULT 'Me',
        amount REAL NOT NULL,
        request_date TEXT NOT NULL,
        payment_date TEXT NOT NULL,
        percentage REAL NOT NULL,
        total_repayment REAL NOT NULL,
        amount_paid REAL DEFAULT 0,
        status TEXT DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>
