<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinish = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

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
            $this->users->remove($applyUser);
        }else{
            $this->users->add($applyUser);
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->turnToUTF8([
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

    /*
     *transform les entités (objet ou string) au format UTF 8
     */
    function turnToUTF8($d) {
        if (is_array($d))
            foreach ($d as $k => $v)
                $d[$k] = $this->turnToUTF8($v);

        else if(is_object($d))
            foreach ($d as $k => $v)
                $d->$k = $this->turnToUTF8($v);

        else
            return utf8_encode($d);

        return $d;
    }


}
