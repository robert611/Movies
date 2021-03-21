<?php 

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class UploadFileService
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function uploadFile(object $file, string $path): bool
    {
        $newFilename = $this->getNewFileName($file);

        try {
            $file->move(
                $path,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload

            return false;
        }

        return true;
    }

    public function deleteFile(string $path): bool
    {
        if (file_exists($path) && is_file($path))
        {
            unlink($path);

            return true;
        }

        return false;
    }

    public function isNameAlreadyTaken(object $file, string $path): bool
    {
        $newFilename = $this->getNewFileName($file);

        return file_exists($path . "/" .$newFilename);
    }

    private function getNewFileName(object $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'.'.$file->guessExtension();

        return $newFilename;
    }
}