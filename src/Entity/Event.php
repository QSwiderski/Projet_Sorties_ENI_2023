<?php

namespace App\Entity;

use App\Repository\EventRepository;
use App\ToolKitBQP;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[Assert\GreaterThan(propertyPath: "dateLimit", message: "La date de début d'événement doit être après la date limite d'inscription!")]
    #[Assert\LessThan(propertyPath: "dateFinish", message: "La date de début d'événement doit être avant la date de fin de l'événement!")]
    #[Assert\GreaterThan(new DateTime('now'), message:"La date de l'événement ne peux être antérieur à la date du jour !")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[Assert\GreaterThan(propertyPath: "dateLimit", message: "La date de fin d'événement doit être après la date limite d'inscription!")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinish = null;

    #[Assert\LessThan(propertyPath: "dateStart", message: "La date limite d'inscription doit être avant la date de début de l'événement!")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\GreaterThanOrEqual(1, message: "Le nombre de participant ne peux être inférieur à un personne !")]
    #[ORM\Column(nullable: true)]
    private ?int $peopleMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cancelReason = null;

    #[ORM\ManyToOne(inversedBy: 'organizedEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'events')]
    private Collection $users;

    #[ORM\Column(nullable: false)]
    private ?bool $isPublished = false;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateFinish(): ?\DateTimeInterface
    {
        return $this->dateFinish;
    }

    public function setDateFinish(\DateTimeInterface $dateFinish): self
    {
        $this->dateFinish = $dateFinish;

        return $this;
    }

    public function getDateLimit(): ?\DateTimeInterface
    {
        return $this->dateLimit;
    }

    public function setDateLimit(\DateTimeInterface $dateLimit): self
    {
        $this->dateLimit = $dateLimit;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPeopleMax(): ?int
    {
        return $this->peopleMax;
    }

    public function setPeopleMax(?int $peopleMax): self
    {
        $this->peopleMax = $peopleMax;

        return $this;
    }

    public function getCancelReason(): ?string
    {
        return $this->cancelReason;
    }

    public function setCancelReason(?string $cancelReason): self
    {
        $this->cancelReason = $cancelReason;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;
        //auto apply pour son event
        $this->addUser($organizer);
        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addEvent($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeEvent($this);
        }

        return $this;
    }

    private function getUsersAsArray(){
        $usersAsArray = [];
        foreach($this->users as $user){
            $usersAsArray[] = $user->getEmail();
        }
        return $usersAsArray;
    }

    /*
     * ajouter-retirer un utilisateur
     */
    public function apply($applyUser){
        $alreadyin=false;
        foreach($this->users as $user){
            if ($applyUser->getId() == $user->getId()){
                $alreadyin = true;
            }
        }
        if ($alreadyin){
            $this->removeUser($applyUser);
        }else if ($this->peopleMax == null || $this->getRoom()>0){
            $this->addUser($applyUser);
        }else {
            return null;
        }
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        $tool = new toolKitBQP();
        return $tool->turnToUTF8([
            'organizer' => $this->organizer->getEmail(),
            'school'=> $this->organizer->getSchool()->getName(),
            'locationID' => $this->location->getId(),
            'locationName' => $this->location->getName(),
            'name' => $this->name,
            'dateStart' => $this->dateStart->format('Y-m-d H:i:s'),
            'dateFinish' => $this->dateFinish->format('Y-m-d H:i:s'),
            'dateLimit' => $this->dateLimit->format('Y-m-d H:i:s'),
            'description' => $this->description,
            'peopleMax' =>$this->peopleMax,
            'users'=> $this->getUsersAsArray()
        ]);
    }

    public function getRoom(){
        if($this->peopleMax == null){
            return null;
        }
        return $this->peopleMax - $this->users->count();
    }

    public function getState(): string
    {
        $today=new DateTime('now');
        if($this->dateFinish < $today){
            return 'FINISHED';
        }else if($this->dateStart < $today){
            return 'STARTED';
        }else if($this->dateLimit < $today){
            return 'CLOSED';
        }else if($this->isPublished){
            return 'PUBLISHED';
        }else{
            return 'UNPUBLISHED';
        }

    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

}
