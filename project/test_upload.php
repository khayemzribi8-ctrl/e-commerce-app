<?php
// Test script to check upload directory permissions and setup

$uploadDir = __DIR__ . '/public/uploads/products';

echo "=== Upload Directory Test ===\n\n";

echo "1. Checking if directory exists: ";
if (is_dir($uploadDir)) {
    echo "✓ YES\n";
} else {
    echo "✗ NO - Attempting to create...\n";
    if (@mkdir($uploadDir, 0777, true)) {
        echo "   ✓ Created successfully\n";
    } else {
        echo "   ✗ Failed to create\n";
    }
}

echo "\n2. Directory permissions:\n";
if (is_dir($uploadDir)) {
    $perms = substr(sprintf('%o', fileperms($uploadDir)), -4);
    echo "   Current: " . $perms . "\n";
    echo "   Readable: " . (is_readable($uploadDir) ? "✓ YES" : "✗ NO") . "\n";
    echo "   Writable: " . (is_writable($uploadDir) ? "✓ YES" : "✗ NO") . "\n";
}

echo "\n3. Parent directory info:\n";
$parentDir = dirname($uploadDir);
echo "   Path: " . $parentDir . "\n";
echo "   Exists: " . (is_dir($parentDir) ? "✓ YES" : "✗ NO") . "\n";
echo "   Writable: " . (is_writable($parentDir) ? "✓ YES" : "✗ NO") . "\n";

echo "\n4. Attempting to create test file...\n";
if (is_dir($uploadDir) && is_writable($uploadDir)) {
    $testFile = $uploadDir . '/test_' . time() . '.txt';
    if (file_put_contents($testFile, 'test') !== false) {
        echo "   ✓ Test file created: " . basename($testFile) . "\n";
        unlink($testFile);
        echo "   ✓ Test file deleted\n";
    } else {
        echo "   ✗ Failed to create test file\n";
    }
}

echo "\n=== END TEST ===\n";
?>
