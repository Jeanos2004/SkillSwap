<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'offerer')]
    private Collection $offeredExchanges;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'receiver')]
    private Collection $receivedExchanges;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'reviewer')]
    private Collection $givenReviews;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'reviewee')]
    private Collection $receivedReviews;

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->offeredExchanges = new ArrayCollection();
        $this->receivedExchanges = new ArrayCollection();
        $this->givenReviews = new ArrayCollection();
        $this->receivedReviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Exchange>
     */
    public function getOfferedExchanges(): Collection
    {
        return $this->offeredExchanges;
    }

    public function addOfferedExchange(Exchange $exchange): static
    {
        if (!$this->offeredExchanges->contains($exchange)) {
            $this->offeredExchanges->add($exchange);
            $exchange->setOfferer($this);
        }

        return $this;
    }

    public function removeOfferedExchange(Exchange $exchange): static
    {
        if ($this->offeredExchanges->removeElement($exchange)) {
            // set the owning side to null (unless already changed)
            if ($exchange->getOfferer() === $this) {
                $exchange->setOfferer(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Exchange>
     */
    public function getReceivedExchanges(): Collection
    {
        return $this->receivedExchanges;
    }

    public function addReceivedExchange(Exchange $exchange): static
    {
        if (!$this->receivedExchanges->contains($exchange)) {
            $this->receivedExchanges->add($exchange);
            $exchange->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedExchange(Exchange $exchange): static
    {
        if ($this->receivedExchanges->removeElement($exchange)) {
            // set the owning side to null (unless already changed)
            if ($exchange->getReceiver() === $this) {
                $exchange->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getGivenReviews(): Collection
    {
        return $this->givenReviews;
    }

    public function addGivenReview(Review $review): static
    {
        if (!$this->givenReviews->contains($review)) {
            $this->givenReviews->add($review);
            $review->setReviewer($this);
        }

        return $this;
    }

    public function removeGivenReview(Review $review): static
    {
        if ($this->givenReviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getReviewer() === $this) {
                $review->setReviewer(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Review>
     */
    public function getReceivedReviews(): Collection
    {
        return $this->receivedReviews;
    }

    public function addReceivedReview(Review $review): static
    {
        if (!$this->receivedReviews->contains($review)) {
            $this->receivedReviews->add($review);
            $review->setReviewee($this);
        }

        return $this;
    }

    public function removeReceivedReview(Review $review): static
    {
        if ($this->receivedReviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getReviewee() === $this) {
                $review->setReviewee(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
