<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
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
    private ?ParkingSpace $space = null;

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

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(choices: ['active', 'cancelled'])]
    private string $status = 'active';

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

    public function getSpace(): ?ParkingSpace
    {
        return $this->space;
    }

    public function setSpace(?ParkingSpace $space): self
    {
        $this->space = $space;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
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
