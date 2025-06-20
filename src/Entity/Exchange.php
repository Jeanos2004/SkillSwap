<?php

namespace App\Entity;

use App\Repository\ExchangeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRepository::class)]
class Exchange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'offeredExchanges')]
    private ?User $offerer = null;

    #[ORM\ManyToOne(inversedBy: 'receivedExchanges')]
    private ?User $receiver = null;

    #[ORM\ManyToOne(inversedBy: 'offeredInExchanges')]
    private ?Skill $offeredSkill = null;

    #[ORM\ManyToOne(inversedBy: 'requestedInExchanges')]
    private ?Skill $requestedSkill = null;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'exchange')]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOfferer(): ?User
    {
        return $this->offerer;
    }

    public function setOfferer(?User $offerer): static
    {
        $this->offerer = $offerer;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getOfferedSkill(): ?Skill
    {
        return $this->offeredSkill;
    }

    public function setOfferedSkill(?Skill $offeredSkill): static
    {
        $this->offeredSkill = $offeredSkill;

        return $this;
    }

    public function getRequestedSkill(): ?Skill
    {
        return $this->requestedSkill;
    }

    public function setRequestedSkill(?Skill $requestedSkill): static
    {
        $this->requestedSkill = $requestedSkill;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setExchange($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getExchange() === $this) {
                $review->setExchange(null);
            }
        }

        return $this;
    }
}
