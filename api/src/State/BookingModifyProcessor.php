<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\BookingModifiedResponse;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;

class BookingModifyProcessor implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Booking
    {
        assert($data instanceof Booking);

        return $this->entityManager->wrapInTransaction(function (EntityManagerInterface $em) use ($data) {

            # Step 1: Ensure this booking exists and is for the current customer
            $existingBooking = $em->getRepository(Booking::class)->find($data->getId());
            if ($existingBooking?->getCustomer()?->getId() !== $data->getCustomer()?->getId()) {
                throw new \Exception('You cannot modify a booking that does not belong to you');
            }

            /**
             * Step 2: Find a parking space ID without overlapping bookings (excluding this one)
             * This might result in their booked space changing unnecessarily, but that doesn't
             * seem super important, and if it was we could cater for it
             */
            $availableSpace = $em->createQueryBuilder()
                ->select('ps')
                ->from('App\Entity\ParkingSpace', 'ps')
                ->leftJoin(
                    'App\Entity\Booking',
                    'b',
                    'WITH',
                    'b.parkingSpace = ps.id AND b.dateFrom <= :dateTo AND b.dateTo >= :dateFrom AND b.id != :bookingId'
                )
                ->where('b.id IS NULL')
                ->setParameter('dateFrom', $data->getDateFrom())
                ->setParameter('dateTo', $data->getDateTo())
                ->setParameter('bookingId', $data->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($availableSpace === null) {
                throw new \Exception('No available parking space found for the given dates');
            }

            # Step 3: Calculate the total price for the new booking
            $price = $em->createQueryBuilder()
                ->select('SUM(dp.price) AS totalPrice')
                ->from('App\Entity\DailyPrice', 'dp')
                ->where('dp.date >= :dateFrom AND dp.date <= :dateTo')
                ->setParameter('dateFrom', $data->getDateFrom())
                ->setParameter('dateTo', $data->getDateTo())
                ->getQuery()
                ->getSingleScalarResult();

            # Step 4: Update the booking record
            $data->setParkingSpace($availableSpace);
            $data->setTotalPrice($price);
            $em->persist($data);

            return $data;
        });
    }
}
