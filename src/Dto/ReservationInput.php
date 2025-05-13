<?php


namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ReservationInput
{


    #[Groups(['reservation:write'])]
    #[Assert\NotBlank(message: 'L’identifiant de la voiture est obligatoire.')]
    #[Assert\Type(type: 'integer', message: 'L’identifiant de la voiture doit être un nombre entier.')]
    #[ApiProperty(example: 1)]
    public ?int $car = null;


    #[Groups(['reservation:write'])]
    #[Assert\NotBlank(message: 'La date de début est obligatoire.')]
    #[Assert\Date(message: 'La date de début doit être au format YYYY-MM-DD.')]
    #[ApiProperty(
        example: "2025-05-13"
    )]
    public ?string $startDate = null;


    #[Groups(['reservation:write'])]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire.')]
    #[Assert\Date(message: 'La date de fin doit être au format YYYY-MM-DD.')]
    #[ApiProperty(
        example: "2025-05-14"
    )]
    public ?string $endDate = null;
}
