<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[ORM\Entity]
class PricingRule
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $name = '';

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
    #[Assert\NotBlank]
    private float $weekdayPrice = 0.0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    private float $weekendPrice = 0.0;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private int $priority = 0;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getWeekdayPrice(): float
    {
        return $this->weekdayPrice;
    }

    public function setWeekdayPrice(float $weekdayPrice): self
    {
        $this->weekdayPrice = $weekdayPrice;
        return $this;
    }

    public function getWeekendPrice(): float
    {
        return $this->weekendPrice;
    }

    public function setWeekendPrice(float $weekendPrice): self
    {
        $this->weekendPrice = $weekendPrice;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }
}
