<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $targetDir = $this->getTargetDirectory();
        
        // Vérifier et créer le répertoire s'il n'existe pas
        if (!is_dir($targetDir)) {
            try {
                @mkdir($targetDir, 0777, true);
                chmod($targetDir, 0777);
            } catch (\Exception $e) {
                throw new \RuntimeException('Impossible de créer le répertoire d\'upload: ' . $e->getMessage());
            }
        }
        
        // Vérifier les permissions
        if (!is_writable($targetDir)) {
            throw new \RuntimeException('Le répertoire d\'upload n\'est pas accessible en écriture: ' . $targetDir);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($targetDir, $newFilename);
            chmod($targetDir . '/' . $newFilename, 0644);
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de l\'upload: ' . $e->getMessage());
        }

        return $newFilename;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
