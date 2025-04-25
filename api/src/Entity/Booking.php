<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\State\BookingModifyProcessor;
use App\State\BookingCreateProcessor;

#[ApiResource(
    operations: [
        /**
         * Let the automatic APi deal with the getters and delete, even though
         * in a real API we'd only let people see & modify their own bookings
         */
        new Get(),
        new GetCollection(),
        new Delete(),
        # Create custom processors for our create & modify operations
        new Post(
            processor: BookingCreateProcessor::class
        ),
        new Patch(
            processor: BookingModifyProcessor::class
        ),
    ]
)]
#[ORM\Entity]
class Booking
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: ParkingSpace::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ParkingSpace $parkingSpace;

    #[ORM\Column(type: 'string', length: 8)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{4}\d{2}\d{2}$/',
        message: 'The dateFrom must be in the format yyyymmdd.'
    )]
    private ?string $dateFrom = null;

    #[ORM\Column(type: 'string', length: 8)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{4}\d{2}\d{2}$/',
        message: 'The dateTo must be in the format yyyymmdd.'
    )]
    private ?string $dateTo = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $totalPrice = 0.0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function getParkingSpace(): ParkingSpace
    {
        return $this->parkingSpace;
    }

    public function setParkingSpace(?ParkingSpace $parkingSpace): self
    {
        $this->parkingSpace = $parkingSpace;
        return $this;
    }

    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }

    public function setDateFrom(string $dateFrom): self
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    public function getDateTo(): ?string
    {
        return $this->dateTo;
    }

    public function setDateTo(string $dateTo): self
    {
        $this->dateTo = $dateTo;
        return $this;
    }


    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }
}
