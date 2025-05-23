<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\ReservationInput;
use App\Provider\UserReservationsProvider;
use App\Repository\ReservationRepository;
use App\State\ReservationProcessor;
use App\State\ReservationUpdateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: "reservations")]


#[ApiResource(
    normalizationContext: ['groups' => ['reservation:read']],
    denormalizationContext: ['groups' => ['reservation:write']],
    operations: [
        new Post(
            input: ReservationInput::class,
            processor: ReservationProcessor::class,
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            uriTemplate: '/users/{userId}/reservations',
            uriVariables: [
                'userId' => new Link(fromClass: User::class, toProperty: 'user')
            ],
            provider: UserReservationsProvider::class,
            normalizationContext: ['groups' => ['reservation:read']],
        ),
        new Put(
            input: ReservationInput::class,
            processor: ReservationUpdateProcessor::class,
        ),
        new Delete(
            security: 'object.getUser() == user'
        ),
    ]
)]

class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?\DateTime $startDate = null;

    #[ORM\Column]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?\DateTime $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?Car $car = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

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
}
