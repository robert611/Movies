<?php 

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format_studio_name', [$this, 'formatStudioName']),
        ];
    }

    public function formatStudioName($studioName): String
    {
        $studioName = str_replace("_", " ", $studioName);
        $studioName = ucwords($studioName);

        if ($studioName == 'Disney Xd') $studioName = 'Disney XD';

        return $studioName;
    }
}