<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserReservationsProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $userId = $uriVariables['userId'] ?? null;

        $user = $this->em->getRepository(User::class)->find($userId);

        if (!$user) {
            throw new NotFoundHttpException("utilisateur avec  $userId est introuvable.");
        }

        return $this->em->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
