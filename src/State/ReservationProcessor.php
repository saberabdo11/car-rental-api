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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ReservationProcessor implements ProcessorInterface
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
            throw new \RuntimeException('Invalid input data.');
        }

        $car = $this->em->getRepository(Car::class)->find($data->car);
        if (!$car) {
            throw new BadRequestHttpException("Voiture introuvable.");
        }

        $start = new \DateTime($data->startDate);
        $end = new \DateTime($data->endDate);
        $user = $this->security->getUser();

        $this->validator->validateDates($start, $end);
        $this->validator->assertCarIsAvailable($car, $start, $end);

        $reservation = $this->factory->create($user, $car, $start, $end);

        $this->em->persist($reservation);
        $this->em->flush();

        return $reservation;
    }
}
