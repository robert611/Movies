<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\ShowLinks;

class EpisodeLinkService
{
    private EntityManagerInterface $entityManager;
    private Session $session;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->session = new Session();
    }

    public function saveEpisodeLinks(object $request, string $showDatabaseTableName, string $episodeId): void
    {
        $linkUrls = $request->request->get('links_urls');
        $linkUrlsNames = $request->request->get('links_urls_names');

        $savedLinkUrls = array();

        foreach ($linkUrls as $key => $linkUrl)
        {
            $linkUrlName = $linkUrlsNames[$key];

            if (!$this->validateData($linkUrl, $linkUrlName))
            {
                $this->session->getFlashBag()->add('admin_error', 'One of the links data are invalid');
                continue;
            }

            if (in_array($linkUrl, $savedLinkUrls))
            {
                $this->session->getFlashBag()->add('admin_error', 'There is already a link with that url: ' . $linkUrl);
                continue;
            }

            $showLink = new ShowLinks();

            $showLink->setName($linkUrlName);
            $showLink->setLink($linkUrl);
            $showLink->setShowDatabaseTableName($showDatabaseTableName);
            $showLink->setEpisodeId($episodeId);
            $showLink->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));

            $this->entityManager->persist($showLink);

            $this->session->getFlashBag()->add('admin_success', "Link with name: ${linkUrlName} has been added");

            $savedLinkUrls[] = $linkUrl;
        }

        $this->entityManager->flush();
    }

    public function validateData(string $linkUrl, string $linkUrlName): bool
    {
        if ($linkUrl == null or $linkUrlName == null) return false;

        if (empty($linkUrl) or empty($linkUrlName)) return false;

        return true;
    }
}