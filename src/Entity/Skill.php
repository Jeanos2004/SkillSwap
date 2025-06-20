<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'skills')]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'offeredSkill')]
    private Collection $offeredInExchanges;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'requestedSkill')]
    private Collection $requestedInExchanges;

    public function __construct()
    {
        $this->offeredInExchanges = new ArrayCollection();
        $this->requestedInExchanges = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Exchange>
     */
    public function getOfferedInExchanges(): Collection
    {
        return $this->offeredInExchanges;
    }

    public function addOfferedInExchange(Exchange $exchange): static
    {
        if (!$this->offeredInExchanges->contains($exchange)) {
            $this->offeredInExchanges->add($exchange);
            $exchange->setOfferedSkill($this);
        }

        return $this;
    }

    public function removeOfferedInExchange(Exchange $exchange): static
    {
        if ($this->offeredInExchanges->removeElement($exchange)) {
            // set the owning side to null (unless already changed)
            if ($exchange->getOfferedSkill() === $this) {
                $exchange->setOfferedSkill(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Exchange>
     */
    public function getRequestedInExchanges(): Collection
    {
        return $this->requestedInExchanges;
    }

    public function addRequestedInExchange(Exchange $exchange): static
    {
        if (!$this->requestedInExchanges->contains($exchange)) {
            $this->requestedInExchanges->add($exchange);
            $exchange->setRequestedSkill($this);
        }

        return $this;
    }

    public function removeRequestedInExchange(Exchange $exchange): static
    {
        if ($this->requestedInExchanges->removeElement($exchange)) {
            // set the owning side to null (unless already changed)
            if ($exchange->getRequestedSkill() === $this) {
                $exchange->setRequestedSkill(null);
            }
        }

        return $this;
    }
}
