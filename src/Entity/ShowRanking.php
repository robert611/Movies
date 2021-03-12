<?php

namespace App\Entity;

use App\Repository\ShowRankingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShowRankingRepository::class)
 */
class ShowRanking
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
    private $show_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $show_database_table_name;

    /**
     * @ORM\Column(type="string", length=48, nullable=true)
     */
    private $votes_up;

    /**
     * @ORM\Column(type="string", length=48, nullable=true)
     */
    private $votes_down;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShowName(): ?string
    {
        return $this->show_name;
    }

    public function setShowName(string $show_name): self
    {
        $this->show_name = $show_name;

        return $this;
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

    public function getVotesUp(): ?string
    {
        return $this->votes_up;
    }

    public function setVotesUp(?string $votes_up): self
    {
        $this->votes_up = $votes_up;

        return $this;
    }

    public function getVotesDown(): ?string
    {
        return $this->votes_down;
    }

    public function setVotesDown(?string $votes_down): self
    {
        $this->votes_down = $votes_down;

        return $this;
    }
}
