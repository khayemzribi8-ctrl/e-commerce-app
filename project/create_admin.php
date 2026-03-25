<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=ecom;charset=utf8mb4', 'root', '');
$hash = '$2y$13$oS3KZgG3GDzxTt2rmj1L.ul4bazDmMaYjdkGQVvKybuCJXCwfdTZu';

$sql = "INSERT INTO user (email, roles, password, first_name, last_name, phone, address, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

$stmt = $db->prepare($sql);
$stmt->execute(['admin@example.com', json_encode(['ROLE_ADMIN', 'ROLE_USER']), $hash, 'Admin', 'User', '', '']);

echo "Admin user created successfully\n";
?>
