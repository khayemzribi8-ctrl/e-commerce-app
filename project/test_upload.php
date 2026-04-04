<?php

$uploadDir = __DIR__ . '/public/uploads/products';

echo "=== Upload Directory Test ===\n\n";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

echo "Directory exists: " . (is_dir($uploadDir) ? "YES" : "NO") . "\n";
echo "Writable: " . (is_writable($uploadDir) ? "YES" : "NO") . "\n";

$testFile = $uploadDir . '/test_' . time() . '.txt';

if (file_put_contents($testFile, 'test') !== false) {
    echo "Test file created\n";

    // 🔒 suppression sécurisée
    $baseDir = realpath($uploadDir);
    $filePath = realpath($testFile);

    if (
        $filePath !== false &&
        str_starts_with($filePath, $baseDir) &&
        file_exists($filePath)
    ) {
        unlink($filePath);
        echo "Test file deleted safely\n";
    }

} else {
    echo "Failed to create test file\n";
}

echo "\n=== END TEST ===\n";
