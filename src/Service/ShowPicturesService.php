<?php 

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class ShowPicturesService
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function uploadPicture(object $picture, string $path): bool
    {
        $newFilename = $this->getPictureNewFileName($picture);

        try {
            $picture->move(
                $path,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload

            return false;
        }

        return true;
    }

    public function isNameAlreadyTaken(object $picture, string $path): bool
    {
        $newFilename = $this->getPictureNewFileName($picture);

        return file_exists($path."/".$newFilename);
    }

    private function getPictureNewFileName(object $picture): string
    {
        $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);

        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'.'.$picture->guessExtension();

        return $newFilename;
    }
}