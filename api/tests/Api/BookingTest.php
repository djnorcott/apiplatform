<?php


namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Booking;
use App\Entity\Customer;
use App\Entity\DailyPrice;
use App\Entity\ParkingSpace;
use App\Entity\PricingRule;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class BookingTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    private Client $client;
    private EntityManagerInterface $entityManager;


    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        /** Set up a simple price matrix with different prices for each date */
        $dailyPrices = [
            '20250801' => 1.0,
            '20250802' => 2.0,
            '20250803' => 3.0,
            '20250804' => 4.0,
            '20250805' => 5.0,
            '20250806' => 6.0,
            '20250807' => 7.0,
            '20250808' => 8.0,
            '20250809' => 9.0,
            '20250810' => 10.0,
        ];

        foreach ($dailyPrices as $date => $price) {
            $pricingRule = new PricingRule();
            $pricingRule->setDateFrom($date);
            $pricingRule->setDateTo($date);
            $pricingRule->setName($date);
            $pricingRule->setWeekdayPrice($price);
            $pricingRule->setWeekendPrice($price);
            $pricingRule->setPriority(1);
            $this->entityManager->persist($pricingRule);

            $dailyPrice = new DailyPrice();
            $dailyPrice->setDate($date);
            $dailyPrice->setPrice($price);
            $dailyPrice->setPricingRule($pricingRule);

            $this->entityManager->persist($dailyPrice);
        }

        # Set up a customer
        $customer = new Customer();
        $customer->setName('John Doe');
        $this->entityManager->persist($customer);

        # Set up a couple of parking spaces
        $parkingSpace = new ParkingSpace();
        $parkingSpace->setLabel('A1');
        $this->entityManager->persist($parkingSpace);

        $parkingSpace2 = new ParkingSpace();
        $parkingSpace2->setLabel('A2');
        $this->entityManager->persist($parkingSpace2);

        $this->entityManager->flush();

        # Set up a couple of partially overlapping bookings
        $booking = new Booking();
        $booking->setDateFrom('20250805');
        $booking->setDateTo('20250810');
        $booking->setCustomer($customer);
        $booking->setParkingSpace($parkingSpace);
        $this->entityManager->persist($booking);

        $booking2 = new Booking();
        $booking2->setDateFrom('20250807');
        $booking2->setDateTo('20250809');
        $booking2->setCustomer($customer);
        $booking2->setParkingSpace($parkingSpace2);
        $this->entityManager->persist($booking2);

        $this->entityManager->flush();
    }


    /**
     * Ensure that we can book a space successfully when both are free
     */
    public function testBookingRequest1(): void
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['name' => 'John Doe']);

        $response = $this->client->request('POST', '/bookings', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'dateFrom' => '20250801',
                'dateTo' => '20250802',
                'customer' => '/customers/' . $customer->getId(),
            ],
        ]);

        self::assertResponseIsSuccessful();

        $data = $response->toArray();
        self::assertSame($data['totalPrice'], 3);
    }

    /**
     * Ensure we can book a space successfully when only one is free
     */
    public function testBookingRequest2(): void
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['name' => 'John Doe']);

        $response = $this->client->request('POST', '/bookings', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'dateFrom' => '20250801',
                'dateTo' => '20250803',
                'customer' => '/customers/' . $customer->getId(),
            ],
        ]);

        self::assertResponseIsSuccessful();

        $data = $response->toArray();
        self::assertSame($data['totalPrice'], 6);
    }


    /**
     * Ensure we cannot book a space when both are booked
     */
    public function testBookingRequest3(): void
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['name' => 'John Doe']);

        $response = $this->client->request('POST', '/bookings', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'dateFrom' => '20250805',
                'dateTo' => '20250810',
                'customer' => '/customers/' . $customer->getId(),
            ],
        ]);

        self::assertResponseStatusCodeSame(500);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No available parking space found for the given dates');

        // Trigger the exception
        $response->toArray();
    }
}
