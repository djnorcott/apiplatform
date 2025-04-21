<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DailyPrice
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 8)]
    private string $date;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price = 0.0;

    #[ORM\ManyToOne(targetEntity: PricingRule::class, inversedBy: 'dailyPrices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PricingRule $pricingRule = null;

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getPricingRule(): ?PricingRule
    {
        return $this->pricingRule;
    }

    public function setPricingRule(?PricingRule $pricingRule): self
    {
        $this->pricingRule = $pricingRule;
        return $this;
    }
}
