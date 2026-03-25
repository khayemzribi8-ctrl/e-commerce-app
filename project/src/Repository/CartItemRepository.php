<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function findByUserAndProduct($user, $product)
    {
        return $this->findOneBy(['user' => $user, 'product' => $product]);
    }

    public function findByUser($user)
    {
        return $this->findBy(['user' => $user]);
    }
}
