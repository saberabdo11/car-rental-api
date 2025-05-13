<?php


namespace App\Services;

use App\Entity\Car;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReservationValidator
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function validateDates(\DateTimeInterface $start, \DateTimeInterface $end): void
    {
        $today = (new \DateTime())->setTime(0, 0, 0);

        if ($start < $today) {
            throw new BadRequestHttpException("La date de début ne peut pas être dans le passé.");
        }

        if ($end <= $start) {
            throw new BadRequestHttpException("La date de fin doit être postérieure à la date de début.");
        }
    }

    public function assertCarIsAvailable(Car $car, \DateTimeInterface $start, \DateTimeInterface $end): void
    {
        $overlap = $this->em->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->andWhere('r.car = :car')
            ->andWhere('r.endDate > :start')
            ->andWhere('r.startDate < :end')
            ->setParameter('car', $car)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        if (!empty($overlap)) {
            throw new BadRequestHttpException("Cette voiture est déjà réservée pour cette période.");
        }
    }
}
