<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/products')]
class ProductAdminController extends AbstractController
{
    #[Route('', name: 'admin_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'admin_product_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        FileUploadService $fileUploadService
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $filename = $fileUploadService->upload($imageFile);
                $product->setImage('uploads/products/' . $filename);
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit créé avec succès!');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_product_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(
        Product $product,
        Request $request,
        EntityManagerInterface $em,
        FileUploadService $fileUploadService
    ): Response {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // 🔒 Suppression sécurisée de l'ancienne image
                if ($product->getImage()) {
                    $this->safeDeleteFile($product->getImage());
                }

                $filename = $fileUploadService->upload($imageFile);
                $product->setImage('uploads/products/' . $filename);
            }

            $product->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès!');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_product_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(
        Product $product,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {

            // 🔒 Suppression sécurisée
            if ($product->getImage()) {
                $this->safeDeleteFile($product->getImage());
            }

            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'Produit supprimé avec succès!');
        }

        return $this->redirectToRoute('admin_product_index');
    }

    /**
     * 🔒 Suppression sécurisée d’un fichier
     */
    private function safeDeleteFile(string $relativePath): void
    {
        $baseDir = realpath('public/uploads/products');

        if (!$baseDir) {
            return;
        }

        $filename = basename($relativePath);
        $filePath = realpath($baseDir . '/' . $filename);

        if (
            $filePath !== false &&
            str_starts_with($filePath, $baseDir) &&
            file_exists($filePath)
        ) {
            unlink($filePath);
        }
    }
}
