<?php

namespace App\Controller;

use App\Entity\BannerImage;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\BannerImageRepository;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('', name: 'app_landing')]
    public function landing(
        ProductRepository $productRepository,
        BannerImageRepository $bannerRepository,
        EntityManagerInterface $em
    ): Response
    {
        // Redirect admins to admin dashboard
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }

        // Récupérer quelques produits pour la landing
        $products = $productRepository->findBy([], ['id' => 'DESC'], 6);
        $banner = $bannerRepository->getLatest();

        // Si aucune bannière n'existe encore, en créer une par défaut
        if (!$banner) {
            $banner = new BannerImage();
            // Image par défaut (à placer dans public/uploads/products/default-banner.jpg)
            $banner->setImagePath('uploads/products/default-banner.jpg');
            $banner->setTitle('Bienvenue dans notre boutique');
            $banner->setDescription('Découvrez nos nouveautés et profitez de nos meilleures offres.');
            $banner->setButtonText('Voir les produits');
            $banner->setButtonUrl('/shop');

            $em->persist($banner);
            $em->flush();
        }
        
        return $this->render('landing.html.twig', [
            'products' => $products,
            'banner' => $banner,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/shop', name: 'app_home_shop')]
    public function shop(Request $request, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        // Empêcher les admins d'accéder au shop
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Les administrateurs n\'ont pas accès au shop client.');
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 9;
        $offset = ($page - 1) * $limit;

        // Récupérer les produits paginés
        $products = $productRepository->findBy([], ['id' => 'DESC'], $limit, $offset);

        // Compter le total pour calculer le nombre de pages
        $totalProducts = $em->getRepository(Product::class)->count([]);
        $totalPages = (int) ceil($totalProducts / $limit) ?: 1;

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        // Redirection automatique vers landing ou shop selon l'utilisateur
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_product_index');
            }
            return $this->redirectToRoute('app_home_shop');
        }
        return $this->redirectToRoute('app_landing');
    }

    #[Route('/product/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(Product $product): Response
    {
        // Empêcher les admins de voir le détail des produits
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Les administrateurs n\'ont pas accès aux détails produits.');
        }
        
        return $this->render('home/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addToCart(
        Product $product,
        Request $request,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $em
    ): Response {
        // Empêcher les admins d'ajouter au panier
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Les administrateurs n\'ont pas accès au panier.');
        }

        $quantity = (int) $request->request->get('quantity', 1);
        $user = $this->getUser();

        // Check if product already in cart
        $cartItem = $cartItemRepository->findByUserAndProduct($user, $product);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setUser($user);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
        }

        $em->persist($cartItem);
        $em->flush();

        $this->addFlash('success', 'Produit ajouté au panier!');
        return $this->redirectToRoute('app_cart');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/cart', name: 'app_cart')]
    public function cart(CartItemRepository $cartItemRepository): Response
    {
        // Empêcher les admins d'accéder au panier
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Les administrateurs n\'ont pas accès au panier.');
        }

        $cartItems = $cartItemRepository->findByUser($this->getUser());
        $total = 0;
        
        foreach ($cartItems as $item) {
            $total += (float)$item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $this->render('home/cart.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function removeFromCart(
        CartItem $cartItem,
        EntityManagerInterface $em
    ): Response {
        // Empêcher les admins de modifier le panier
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Les administrateurs n\'ont pas accès au panier.');
        }

        if ($cartItem->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($cartItem);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé du panier!');
        return $this->redirectToRoute('app_cart');
    }
}
