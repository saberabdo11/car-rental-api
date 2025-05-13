<?php


namespace App\Services;

use App\Entity\Car;
use App\Entity\Reservation;
use App\Entity\User;

class ReservationFactory
{
    public function create(User $user, Car $car, \DateTimeInterface $start, \DateTimeInterface $end): Reservation
    {
        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setCar($car);
        $reservation->setStartDate($start);
        $reservation->setEndDate($end);

        return $reservation;
    }

    public function update(Reservation $reservation, Car $car, \DateTimeInterface $start, \DateTimeInterface $end): Reservation
    {
        $reservation->setCar($car);
        $reservation->setStartDate($start);
        $reservation->setEndDate($end);
       
        return $reservation;
    }
}
