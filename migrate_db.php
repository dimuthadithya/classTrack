<?php
require_once 'includes/db.php';

try {
    echo "Starting Database Migration to Tuition Model...\n";

    // 1. Create course_months table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_months (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            year INT NOT NULL,
            live_link VARCHAR(255),
            fee DECIMAL(10, 2) DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )
    ");
    echo "- Created 'course_months' table.\n";

    // 2. Drop FKs if they exist (This logic is tricky in raw SQL without knowing names, 
    // so we will attempt simpler ALTERs assuming clean slate or direct table drops if safe.
    // Given this is Dev, let's DROP and Recreate specific tables to ensure structure matches.
    
    // WARNING: This deletes data in enrollments/resources.
    $pdo->exec("DROP TABLE IF EXISTS resources");
    $pdo->exec("DROP TABLE IF EXISTS enrollments");
    echo "- Dropped old 'resources' and 'enrollments' tables.\n";

    // 3. Recreate Enrollments with month_id
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS enrollments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            month_id INT NOT NULL,
            payment_status ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
            enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (month_id) REFERENCES course_months(id) ON DELETE CASCADE
        )
    ");
    echo "- Recreated 'enrollments' table linked to months.\n";

    // 4. Recreate Resources with month_id
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS resources (
            id INT AUTO_INCREMENT PRIMARY KEY,
            month_id INT NOT NULL,
            title VARCHAR(100) NOT NULL,
            type ENUM('recording', 'document') NOT NULL,
            url VARCHAR(255) NOT NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (month_id) REFERENCES course_months(id) ON DELETE CASCADE
        )
    ");
    echo "- Recreated 'resources' table linked to months.\n";
    
    // 5. Cleanup courses table (Remove live_link/price as they are now in months)
    // We'll leave them for now to avoid breaking legacy queries during transition, 
    // but the new logic won't use them.

    echo "Migration Complete!\n";

} catch (PDOException $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
}
?>
