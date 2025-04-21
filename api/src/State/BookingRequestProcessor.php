<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ParkingSpace;
use Doctrine\ORM\EntityManagerInterface;

class BookingRequestProcessor implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $fromDate = \DateTime::createFromFormat('Ymd', $data->from_date);
        $toDate = \DateTime::createFromFormat('Ymd', $data->to_date);

        // Fetch all ParkingSpace records using the repository
        $parkingSpaces = $this->entityManager->getRepository(ParkingSpace::class)->findAll();

        $message = 'The following parking spaces are free: ';
        foreach ($parkingSpaces as $parkingSpace) {
            if ($parkingSpace->getIsActive()) {
                $message .= $parkingSpace->getLabel() . ', ';
            }
        }

        return [
            'from_date' => $data->from_date,
            'to_date' => $data->to_date,
            'message' => $message,
        ];
    }
}
