<?php

// Script simple pour insérer un lot de produits de démonstration
// À exécuter depuis la racine du projet avec :
// php seed_products.php

$dsn = 'mysql:host=127.0.0.1;dbname=ecom;charset=utf8mb4';
$user = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage() . PHP_EOL;
    exit(1);
}

$now = date('Y-m-d H:i:s');

// Tableau de produits de démonstration, répartis par catégories
$products = [
    // Électronique
    [
        'name' => 'Smartphone 5G Pro',
        'category' => 'Électronique',
        'price' => 799.99,
        'stock' => 25,
        'image' => 'https://images.pexels.com/photos/6078123/pexels-photo-6078123.jpeg',
        'description' => 'Smartphone 5G avec écran OLED 6,5", 256 Go de stockage et triple capteur photo.',
    ],
    [
        'name' => 'Casque Bluetooth Noise Cancelling',
        'category' => 'Électronique',
        'price' => 199.90,
        'stock' => 40,
        'image' => 'https://images.pexels.com/photos/3394664/pexels-photo-3394664.jpeg',
        'description' => 'Casque sans fil avec réduction de bruit active et 30h d’autonomie.',
    ],
    [
        'name' => 'Ordinateur Portable 15" Ultra',
        'category' => 'Électronique',
        'price' => 1199.00,
        'stock' => 15,
        'image' => 'https://images.pexels.com/photos/18105/pexels-photo.jpg',
        'description' => 'PC portable 15" avec processeur performant, 16 Go de RAM et SSD 512 Go.',
    ],

    // Maison
    [
        'name' => 'Machine à café automatique',
        'category' => 'Maison',
        'price' => 249.99,
        'stock' => 20,
        'image' => 'https://images.pexels.com/photos/302899/pexels-photo-302899.jpeg',
        'description' => 'Machine à café avec broyeur intégré et mousseur à lait.',
    ],
    [
        'name' => 'Aspirateur sans fil',
        'category' => 'Maison',
        'price' => 159.90,
        'stock' => 30,
        'image' => 'https://images.pexels.com/photos/4107283/pexels-photo-4107283.jpeg',
        'description' => 'Aspirateur léger et puissant, autonomie 45 minutes, idéal pour toute la maison.',
    ],

    // Vêtements
    [
        'name' => 'Veste en jean unisexe',
        'category' => 'Vêtements',
        'price' => 69.90,
        'stock' => 50,
        'image' => 'https://images.pexels.com/photos/7940622/pexels-photo-7940622.jpeg',
        'description' => 'Veste en jean coupe classique, parfaite pour toutes les saisons.',
    ],
    [
        'name' => 'Basket de sport légères',
        'category' => 'Vêtements',
        'price' => 89.99,
        'stock' => 60,
        'image' => 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg',
        'description' => 'Chaussures de sport respirantes, adaptées à la course et au fitness.',
    ],

    // Voiture
    [
        'name' => 'Kit d’entretien voiture complet',
        'category' => 'Voiture',
        'price' => 39.90,
        'stock' => 80,
        'image' => 'https://images.pexels.com/photos/6700149/pexels-photo-6700149.jpeg',
        'description' => 'Kit complet pour nettoyer et entretenir l’intérieur et l’extérieur de votre véhicule.',
    ],
    [
        'name' => 'Support smartphone pour voiture',
        'category' => 'Voiture',
        'price' => 19.99,
        'stock' => 100,
        'image' => 'https://images.pexels.com/photos/125779/pexels-photo-125779.jpeg',
        'description' => 'Support universel pour smartphone, fixation sur pare-brise ou tableau de bord.',
    ],

    // Jardinage
    [
        'name' => 'Kit outils de jardinage 10 pièces',
        'category' => 'Jardinage',
        'price' => 59.90,
        'stock' => 35,
        'image' => 'https://images.pexels.com/photos/4750270/pexels-photo-4750270.jpeg',
        'description' => 'Ensemble d’outils pour entretenir votre jardin et vos plantes.',
    ],
    [
        'name' => 'Arrosoir design 5L',
        'category' => 'Jardinage',
        'price' => 24.99,
        'stock' => 40,
        'image' => 'https://images.pexels.com/photos/4503268/pexels-photo-4503268.jpeg',
        'description' => 'Arrosoir ergonomique, idéal pour le jardin et le balcon.',
    ],

    // Sports
    [
        'name' => 'Tapis de yoga antidérapant',
        'category' => 'Sports',
        'price' => 29.90,
        'stock' => 70,
        'image' => 'https://images.pexels.com/photos/3823063/pexels-photo-3823063.jpeg',
        'description' => 'Tapis de yoga confortable, idéal pour le yoga, le pilates et le fitness.',
    ],
    [
        'name' => 'Haltères réglables 20kg',
        'category' => 'Sports',
        'price' => 149.00,
        'stock' => 25,
        'image' => 'https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg',
        'description' => 'Paire d’haltères réglables pour l’entraînement à domicile.',
    ],

    // Livres
    [
        'name' => 'Roman best-seller',
        'category' => 'Livres',
        'price' => 19.90,
        'stock' => 120,
        'image' => 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg',
        'description' => 'Roman captivant plébiscité par les lecteurs.',
    ],
    [
        'name' => 'Livre de recettes familiales',
        'category' => 'Livres',
        'price' => 24.50,
        'stock' => 80,
        'image' => 'https://images.pexels.com/photos/3184170/pexels-photo-3184170.jpeg',
        'description' => 'Un livre rempli de recettes simples et savoureuses pour tous les jours.',
    ],

    // Jouets
    [
        'name' => 'Jeu de construction pour enfants',
        'category' => 'Jouets',
        'price' => 34.90,
        'stock' => 60,
        'image' => 'https://images.pexels.com/photos/3661390/pexels-photo-3661390.jpeg',
        'description' => 'Jeu éducatif pour développer la créativité et la motricité.',
    ],
    [
        'name' => 'Peluche ourson géant',
        'category' => 'Jouets',
        'price' => 49.99,
        'stock' => 40,
        'image' => 'https://images.pexels.com/photos/207891/pexels-photo-207891.jpeg',
        'description' => 'Grande peluche toute douce, idéale pour les câlins.',
    ],

    // Beauté
    [
        'name' => 'Coffret soins visage',
        'category' => 'Beauté',
        'price' => 59.99,
        'stock' => 50,
        'image' => 'https://images.pexels.com/photos/3738364/pexels-photo-3738364.jpeg',
        'description' => 'Coffret complet pour une routine de soins du visage.',
    ],
    [
        'name' => 'Sèche-cheveux professionnel',
        'category' => 'Beauté',
        'price' => 89.90,
        'stock' => 35,
        'image' => 'https://images.pexels.com/photos/3738341/pexels-photo-3738341.jpeg',
        'description' => 'Sèche-cheveux puissant avec technologie ionique.',
    ],

    // Alimentation
    [
        'name' => 'Panier gourmand',
        'category' => 'Alimentation',
        'price' => 49.90,
        'stock' => 45,
        'image' => 'https://images.pexels.com/photos/102104/pexels-photo-102104.jpeg',
        'description' => 'Sélection de produits sucrés et salés de qualité.',
    ],
    [
        'name' => 'Coffret de thés du monde',
        'category' => 'Alimentation',
        'price' => 29.99,
        'stock' => 70,
        'image' => 'https://images.pexels.com/photos/1417945/pexels-photo-1417945.jpeg',
        'description' => 'Assortiment de thés d’origines variées.',
    ],

    // Santé
    [
        'name' => 'Tensiomètre électronique',
        'category' => 'Santé',
        'price' => 69.90,
        'stock' => 30,
        'image' => 'https://images.pexels.com/photos/6129236/pexels-photo-6129236.jpeg',
        'description' => 'Tensiomètre de bras simple d’utilisation pour suivre votre tension.',
    ],
    [
        'name' => 'Balance connectée',
        'category' => 'Santé',
        'price' => 79.90,
        'stock' => 40,
        'image' => 'https://images.pexels.com/photos/6692115/pexels-photo-6692115.jpeg',
        'description' => 'Balance connectée pour suivre votre poids et votre IMC.',
    ],

    // Autres
    [
        'name' => 'Lampe de bureau LED',
        'category' => 'Autres',
        'price' => 34.90,
        'stock' => 55,
        'image' => 'https://images.pexels.com/photos/1519088/pexels-photo-1519088.jpeg',
        'description' => 'Lampe de bureau moderne avec lumière réglable.',
    ],
    [
        'name' => 'Sac à dos urbain',
        'category' => 'Autres',
        'price' => 59.90,
        'stock' => 45,
        'image' => 'https://images.pexels.com/photos/3747465/pexels-photo-3747465.jpeg',
        'description' => 'Sac à dos pratique et résistant pour le quotidien.',
    ],
];

// Optimiser les images Pexels en utilisant la compression et une largeur limitée
foreach ($products as &$p) {
    if (!isset($p['image'])) {
        continue;
    }

    if (str_starts_with($p['image'], 'https://images.pexels.com/') && !str_contains($p['image'], '?')) {
        $p['image'] .= '?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop';
    }
}
unset($p);

$sql = "INSERT INTO product (name, description, price, stock, image, category, created_at, updated_at)
        VALUES (:name, :description, :price, :stock, :image, :category, :created_at, :updated_at)";

$stmt = $pdo->prepare($sql);

$count = 0;
foreach ($products as $p) {
    $stmt->execute([
        ':name' => $p['name'],
        ':description' => $p['description'],
        ':price' => $p['price'],
        ':stock' => $p['stock'],
        ':image' => $p['image'],
        ':category' => $p['category'],
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);
    $count++;
}

echo "${count} produits ont été insérés avec succès." . PHP_EOL;
