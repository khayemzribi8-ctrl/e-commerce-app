<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartItemRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/checkout', name: 'app_order_checkout', methods: ['POST'])]
    public function checkout(
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $em
    ): Response {
        // Empêcher les admins de passer des commandes
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Les administrateurs ne peuvent pas passer de commandes.');
        }

        $user = $this->getUser();
        $cartItems = $cartItemRepository->findByUser($user);

        if (empty($cartItems)) {
            $this->addFlash('error', 'Votre panier est vide!');
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order();
        $order->setUser($user);
        $totalAmount = 0;

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduct();
            
            // Check stock
            if ($product->getStock() < $cartItem->getQuantity()) {
                $this->addFlash('error', 'Stock insuffisant pour ' . $product->getName());
                return $this->redirectToRoute('app_cart');
            }

            // Create order item
            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($product->getPrice());
            $order->addOrderItem($orderItem);

            // Decrease stock
            $product->setStock($product->getStock() - $cartItem->getQuantity());
            $em->persist($product);

            // Calculate total
            $totalAmount += (float)$product->getPrice() * $cartItem->getQuantity();

            // Remove from cart
            $em->remove($cartItem);
        }

        $order->setTotalAmount((string)$totalAmount);
        $order->setStatus('pending');

        $em->persist($order);
        $em->flush();

        $this->addFlash('success', 'Commande passée avec succès!');
        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }

    #[Route('/{id}', name: 'app_order_show', requirements: ['id' => '\d+'])]
    public function show(Order $order): Response
    {
        // Empêcher les admins de voir les commandes
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Les administrateurs ne peuvent pas voir les commandes client.');
        }

        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('', name: 'app_order_list')]
    public function list(OrderRepository $orderRepository): Response
    {
        // Empêcher les admins d'accéder à la liste des commandes
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Les administrateurs ne peuvent pas accéder aux commandes client.');
        }

        $orders = $orderRepository->findByUser($this->getUser());

        return $this->render('order/list.html.twig', [
            'orders' => $orders,
        ]);
    }
}
