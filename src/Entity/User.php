<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=2048)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Shows::class, mappedBy="user")
     */
    private $shows;

    /**
     * @ORM\OneToMany(targetEntity=UserWatchingHistory::class, mappedBy="user", orphanRemoval=true)
     */
    private $userWatchingHistories;

    public function __construct()
    {
        $this->shows = new ArrayCollection();
        $this->userWatchingHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Shows[]
     */
    public function getShows(): Collection
    {
        return $this->shows;
    }

    public function addShow(Shows $show): self
    {
        if (!$this->shows->contains($show)) {
            $this->shows[] = $show;
            $show->setUser($this);
        }

        return $this;
    }

    public function removeShow(Shows $show): self
    {
        if ($this->shows->removeElement($show)) {
            // set the owning side to null (unless already changed)
            if ($show->getUser() === $this) {
                $show->setUser(null);
            }
        }

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
            $userWatchingHistory->setUser($this);
        }

        return $this;
    }

    public function removeUserWatchingHistory(UserWatchingHistory $userWatchingHistory): self
    {
        if ($this->userWatchingHistories->removeElement($userWatchingHistory)) {
            // set the owning side to null (unless already changed)
            if ($userWatchingHistory->getUser() === $this) {
                $userWatchingHistory->setUser(null);
            }
        }

        return $this;
    }
}
