<?php

namespace App\Entity;

use App\Repository\ShowsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShowsRepository::class)
 */
class Shows
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_watching_history"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_watching_history"})
     */
    private $database_table_name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_watching_history"})
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=2048)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=ShowCategory::class, inversedBy="shows")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Studio::class, inversedBy="shows")
     */
    private $studio;

    /**
     * @ORM\ManyToMany(targetEntity=ShowTheme::class, inversedBy="shows")
     */
    private $themes;

    /**
     * @ORM\OneToMany(targetEntity=UserWatchingHistory::class, mappedBy="series", orphanRemoval=true)
     */
    private $userWatchingHistories;

    public function __construct()
    {
        $this->themes = new ArrayCollection();
        $this->userWatchingHistories = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDatabaseTableName(): ?string
    {
        return $this->database_table_name;
    }

    public function setDatabaseTableName(string $database_table_name): self
    {
        $this->database_table_name = $database_table_name;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at ? $this->updated_at : new \DateTime();
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCategory(): ?ShowCategory
    {
        return $this->category;
    }

    public function setCategory(?ShowCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getStudio(): ?Studio
    {
        return $this->studio;
    }

    public function setStudio(?Studio $studio): self
    {
        $this->studio = $studio;

        return $this;
    }

    /**
     * @return Collection|ShowTheme[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(ShowTheme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
        }

        return $this;
    }

    public function removeTheme(ShowTheme $theme): self
    {
        $this->themes->removeElement($theme);

        return $this;
    }

    /**
     * @return Collection|UserWatchingHistory[]
     */
    public function getUserWatchingHistories(): Collection
    {
        return $this->userWatchingHistories;
    }

    public function addUserWatchingHistory(UserWatchingHistory $userWatchingHistory): self
    {
        if (!$this->userWatchingHistories->contains($userWatchingHistory)) {
            $this->userWatchingHistories[] = $userWatchingHistory;
            $userWatchingHistory->setSeries($this);
        }

        return $this;
    }

    public function removeUserWatchingHistory(UserWatchingHistory $userWatchingHistory): self
    {
        if ($this->userWatchingHistories->removeElement($userWatchingHistory)) {
            // set the owning side to null (unless already changed)
            if ($userWatchingHistory->getSeries() === $this) {
                $userWatchingHistory->setSeries(null);
            }
        }

        return $this;
    }

    public function isInThemes(ShowTheme $searchedTheme): bool
    {
        $themes = $this->getThemes();

        foreach ($themes as $theme)
        {
            if ($theme->getId() == $searchedTheme->getId()) return true;
        }
        
        return false;
    }
}
