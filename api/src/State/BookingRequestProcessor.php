<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\BookingRequestResponse;
use App\Entity\ParkingSpace;
use App\Repository\ParkingSpaceRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookingRequestProcessor implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BookingRequestResponse
    {
        /** @var ParkingSpaceRepository $repository */
        $repository = $this->entityManager->getRepository(ParkingSpace::class);

        $spaces = 0;
        $cost = $repository->findCostOfBooking($data->date_from, $data->date_to);

        # If we've no cost, we can't offer a space, so don't bother looking
        if ($cost !== null) {
            $spaces = $repository->findNumberOfSpacesFreeOnDates($data->date_from, $data->date_to);
        }

        return new BookingRequestResponse(
            $data->date_from,
            $data->date_to,
            $spaces,
            $cost ?? 0.0,
        );
    }
}
