<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ReservationInput;
use App\Entity\Car;
use App\Entity\Reservation;
use App\Services\ReservationFactory;
use App\Services\ReservationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ReservationUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly ReservationValidator $validator,
        private readonly ReservationFactory $factory

    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Reservation
    {
        if (!$data instanceof ReservationInput) {
            throw new \RuntimeException('invalid input');
        }

        $user = $this->security->getUser();

        // Get reservation  from url
        $reservationId = $uriVariables['id'] ?? null;
        if (!$reservationId) {
            throw new NotFoundHttpException("id de reservation manquant");
        }

        $reservation = $this->em->getRepository(Reservation::class)->find($reservationId);
        if (!$reservation) {
            throw new NotFoundHttpException("weservation introuvable.");
        }

        if ($reservation->getUser() !== $user) {
            throw new AccessDeniedException("vous n'avez pas le droit pour modifier reservation");
        }

        $car = $this->em->getRepository(Car::class)->find($data->car);
        if (!$car) {
            throw new NotFoundHttpException("Voiture introuvable");
        }

        $start = new \DateTime($data->startDate);
        $end = new \DateTime($data->endDate);

        $this->validator->validateDates($start, $end);
        $this->validator->assertCarIsAvailable($car, $start, $end, $reservation);


        // update
        $this->factory->update($reservation, $car, $start, $end);
        $this->em->flush();

        return $reservation;
    }
}
