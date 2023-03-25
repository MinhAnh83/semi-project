<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ReviewContent = null;

    #[ORM\Column(length: 255)]
    private ?string $Ranking = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $ReviewDate = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'reviews')]
    private Collection $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReviewContent(): ?string
    {
        return $this->ReviewContent;
    }

    public function setReviewContent(string $ReviewContent): self
    {
        $this->ReviewContent = $ReviewContent;

        return $this;
    }

    public function getRanking(): ?string
    {
        return $this->Ranking;
    }

    public function setRanking(string $Ranking): self
    {
        $this->Ranking = $Ranking;

        return $this;
    }

    public function getReviewDate(): ?\DateTimeInterface
    {
        return $this->ReviewDate;
    }

    public function setReviewDate(\DateTimeInterface $ReviewDate): self
    {
        $this->ReviewDate = $ReviewDate;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }
}
