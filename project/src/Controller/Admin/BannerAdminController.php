<?php

namespace App\Controller\Admin;

use App\Entity\BannerImage;
use App\Form\BannerImageFormType;
use App\Repository\BannerImageRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/banner')]
class BannerAdminController extends AbstractController
{
    #[Route('', name: 'admin_banner_index')]
    public function index(BannerImageRepository $bannerRepository): Response
    {
        $banner = $bannerRepository->getLatest();

        return $this->render('admin/banner/index.html.twig', [
            'banner' => $banner,
        ]);
    }

    #[Route('/new', name: 'admin_banner_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        FileUploadService $fileUploadService
    ): Response {
        $banner = new BannerImage();
        $form = $this->createForm(BannerImageFormType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imagePath')->getData();
            
            if ($imageFile) {
                $filename = $fileUploadService->upload($imageFile);
                $banner->setImagePath('uploads/products/' . $filename);
            }

            $em->persist($banner);
            $em->flush();

            $this->addFlash('success', 'Bannière créée avec succès!');
            return $this->redirectToRoute('admin_banner_index');
        }

        return $this->render('admin/banner/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_banner_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(
        BannerImage $banner,
        Request $request,
        EntityManagerInterface $em,
        FileUploadService $fileUploadService
    ): Response {
        $form = $this->createForm(BannerImageFormType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imagePath')->getData();
            
            if ($imageFile) {
                // Delete old image if exists
                if ($banner->getImagePath() && file_exists('public/' . $banner->getImagePath())) {
                    unlink('public/' . $banner->getImagePath());
                }
                
                $filename = $fileUploadService->upload($imageFile);
                $banner->setImagePath('uploads/products/' . $filename);
            }

            $banner->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Bannière modifiée avec succès!');
            return $this->redirectToRoute('admin_banner_index');
        }

        return $this->render('admin/banner/edit.html.twig', [
            'form' => $form,
            'banner' => $banner,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_banner_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(
        BannerImage $banner,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $banner->getId(), $request->request->get('_token'))) {
            if ($banner->getImagePath() && file_exists('public/' . $banner->getImagePath())) {
                unlink('public/' . $banner->getImagePath());
            }

            $em->remove($banner);
            $em->flush();

            $this->addFlash('success', 'Bannière supprimée avec succès!');
        }

        return $this->redirectToRoute('admin_banner_index');
    }
}
