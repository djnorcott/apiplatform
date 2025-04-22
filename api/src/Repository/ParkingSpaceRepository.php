<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\ParkingSpace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParkingSpace>
 */
class ParkingSpaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParkingSpace::class);
    }

    public function findNumberOfSpacesFreeOnDates(string $dateFrom, string $dateTo): int
    {
        return $this->createQueryBuilder('ps')
            ->select('COUNT(ps.id) AS numberOfFreeSpaces')
            ->leftJoin(
                'App\Entity\Booking',
                'b',
                'WITH',
                'b.parkingSpace = ps.id AND b.dateFrom <= :dateTo AND b.dateTo >= :dateFrom'
            )
            ->where('b.id IS NULL')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function findCostOfBooking(string $dateFrom, string $dateTo): ?float
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(dp.price) AS totalPrice')
            ->from('App\Entity\DailyPrice', 'dp')
            ->where('dp.date >= :dateFrom AND dp.date <= :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
