<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<h2>Super Admin Check</h2>";

// Check if super admin exists
$stmt = db()->prepare('SELECT COUNT(*) as count FROM admins WHERE role = :role');
$stmt->execute(['role' => 'super_admin']);
$result = $stmt->fetch();

echo "<h3>Super Admin Count: " . $result['count'] . "</h3>";

if ($result['count'] > 0) {
    echo "<p><strong>✅ Super admin already exists - creation should be blocked</strong></p>";
    
    // Show existing super admin
    $stmt2 = db()->prepare('SELECT * FROM admins WHERE role = :role');
    $stmt2->execute(['role' => 'super_admin']);
    $superAdmin = $stmt2->fetch();
    
    echo "<h4>Existing Super Admin:</h4>";
    echo "<ul>";
    echo "<li>Name: " . htmlspecialchars($superAdmin['fullname']) . "</li>";
    echo "<li>Email: " . htmlspecialchars($superAdmin['email']) . "</li>";
    echo "<li>Role: " . htmlspecialchars($superAdmin['role']) . "</li>";
    echo "</ul>";
} else {
    echo "<p><strong>❌ No super admin exists - creation should be allowed</strong></p>";
}

// Test the UserController method
require_once __DIR__ . '/controllers/UserController.php';

// We can't call the private method directly, but we can test the logic
echo "<h3>Test Logic:</h3>";
echo "<p>The system should prevent creating additional super admin accounts.</p>";
echo "<p>If a super admin exists, the role selection should be disabled in forms.</p>";

?>
