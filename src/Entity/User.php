<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\Column(length: 30)]
    private ?string $surname = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 80)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'users')]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'organizer', targetEntity: Event::class, orphanRemoval: true)]
    private Collection $organizedEvents;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Credentials $credentials = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->organizedEvents = new ArrayCollection();
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): self
    {
        $this->school = $school;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        $this->events->removeElement($event);

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getOrganizedEvents(): Collection
    {
        return $this->organizedEvents;
    }

    public function addOrganizedEvent(Event $organizedEvent): self
    {
        if (!$this->organizedEvents->contains($organizedEvent)) {
            $this->organizedEvents->add($organizedEvent);
            $organizedEvent->setOrganizer($this);
        }

        return $this;
    }

    public function removeOrganizedEvent(Event $organizedEvent): self
    {
        if ($this->organizedEvents->removeElement($organizedEvent)) {
            // set the owning side to null (unless already changed)
            if ($organizedEvent->getOrganizer() === $this) {
                $organizedEvent->setOrganizer(null);
            }
        }

        return $this;
    }

    public function getCredentials(): ?Credentials
    {
        return $this->credentials;
    }

    public function setCredentials(Credentials $credentials): self
    {
        // set the owning side of the relation if necessary
        if ($credentials->getUser() !== $this) {
            $credentials->setUser($this);
        }

        $this->credentials = $credentials;

        return $this;
    }
}
