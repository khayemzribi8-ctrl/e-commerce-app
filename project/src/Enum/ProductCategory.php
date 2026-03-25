<?php

namespace App\Enum;

class ProductCategory
{
    public const CATEGORIES = [
        'Électronique' => 'Électronique',
        'Maison' => 'Maison',
        'Vêtements' => 'Vêtements',
        'Voiture' => 'Voiture',
        'Jardinage' => 'Jardinage',
        'Sports' => 'Sports',
        'Livres' => 'Livres',
        'Jouets' => 'Jouets',
        'Beauté' => 'Beauté',
        'Alimentation' => 'Alimentation',
        'Santé' => 'Santé',
        'Autres' => 'Autres',
    ];

    public static function getAll(): array
    {
        return self::CATEGORIES;
    }

    public static function getChoices(): array
    {
        return array_flip(self::CATEGORIES);
    }
}
