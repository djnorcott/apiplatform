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
        $cost = $this->entityManager
            ->createQueryBuilder()
            ->select('SUM(dp.price) AS totalPrice')
            ->from('App\Entity\DailyPrice', 'dp')
            ->where('dp.date >= :dateFrom AND dp.date <= :dateTo')
            ->setParameter('dateFrom', $data->date_from)
            ->setParameter('dateTo', $data->date_to)
            ->getQuery()
            ->getSingleScalarResult();

        # If we've no cost, we can't offer a space, so don't bother looking
        if ($cost !== null) {
            $spaces = $this->entityManager->createQueryBuilder()
                ->select('COUNT(ps.id) AS numberOfFreeSpaces')
                ->from('App\Entity\ParkingSpace', 'ps')
                ->leftJoin(
                    'App\Entity\Booking',
                    'b',
                    'WITH',
                    'b.parkingSpace = ps.id AND b.dateFrom <= :dateTo AND b.dateTo >= :dateFrom'
                )
                ->where('b.id IS NULL')
                ->setParameter('dateFrom', $data->date_from)
                ->setParameter('dateTo', $data->date_to)
                ->getQuery()
                ->getSingleScalarResult();
        }

        return new BookingRequestResponse(
            $data->date_from,
            $data->date_to,
            $spaces,
            $cost ?? 0.0,
        );
    }
}
