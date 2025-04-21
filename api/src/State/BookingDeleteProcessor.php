<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;

class BookingDeleteProcessor implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        assert($data instanceof Booking);

        # Step 1: Ensure this booking exists and is for the current customer
        $existingBooking = $this->entityManager->getRepository(Booking::class)->find($data->getId());
        if (!$existingBooking instanceof Booking || $existingBooking->getCustomer()?->getId() !== $data->getCustomer()?->getId()) {
            return ['status' => 'failure', 'message' => 'You cannot delete a booking that does not belong to you'];
        }

        # Step 2: Delete the booking
        $this->entityManager->remove($existingBooking);
        $this->entityManager->flush();

        return ['status' => 'success', 'message' => 'Booking deleted successfully'];
    }
}
