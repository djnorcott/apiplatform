<?php

namespace App\Repository;

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

    /**
     * Example: Find all active parking spaces.
     */
    public function findActiveSpaces(): array
    {
        return $this->createQueryBuilder('ps')
            ->where('ps.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    public function findSpacesFreeOnDates(string $startDate, string $endDate): array
    {
        /**
         * select every parking space where there is not a booking between
         * that start and end date
         */
        return $this->createQueryBuilder('ps')
            ->leftJoin('ps.bookings', 'b')
            ->andWhere('b.dateFrom IS NULL OR b.dateTo IS NULL OR (b.dateFrom > :endDate OR b.dateTo < :startDate)')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }
}
