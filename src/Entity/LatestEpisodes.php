<?php

namespace App\Entity;

use App\Repository\LatestEpisodesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LatestEpisodesRepository::class)
 */
class LatestEpisodes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $show_database_table_name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $episode_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShowDatabaseTableName(): ?string
    {
        return $this->show_database_table_name;
    }

    public function setShowDatabaseTableName(string $show_database_table_name): self
    {
        $this->show_database_table_name = $show_database_table_name;

        return $this;
    }

    public function getEpisodeId(): ?int
    {
        return $this->episode_id;
    }

    public function setEpisodeId(int $episode_id): self
    {
        $this->episode_id = $episode_id;

        return $this;
    }
}
