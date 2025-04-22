<?php


namespace App\Tests\State;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class BuildDailyPricesProcessorTest extends ApiTestCase
{
    private $client;
    private $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        // Retrieve the entity manager from the test container
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Create multiple price rules
        $priceRules = [
            [
                "name" => "2025 normal",
                "dateFrom" => "20250101",
                "dateTo" => "20251231",
                "weekdayPrice" => "12.50",
                "weekendPrice" => "15.25",
                "priority" => 1
            ],
            [
                "name" => "2025 holiday",
                "dateFrom" => "20251224",
                "dateTo" => "20251226",
                "weekdayPrice" => "20.00",
                "weekendPrice" => "25.00",
                "priority" => 2
            ],
            [
                "name" => "2025 summer",
                "dateFrom" => "20250601",
                "dateTo" => "20250831",
                "weekdayPrice" => "18.00",
                "weekendPrice" => "22.00",
                "priority" => 1
            ],
        ];

        // Store these in the entity manager
        foreach ($priceRules as $ruleData) {
            $rule = new \App\Entity\PricingRule();
            $rule->setName($ruleData['name']);
            $rule->setDateFrom($ruleData['dateFrom']);
            $rule->setDateTo($ruleData['dateTo']);
            $rule->setWeekdayPrice($ruleData['weekdayPrice']);
            $rule->setWeekendPrice($ruleData['weekendPrice']);
            $rule->setPriority($ruleData['priority']);

            $this->entityManager->persist($rule);
        }
    }

    public function testBuiltDailyPrices(): void
    {
        $this->client->request('POST', '/daily_prices/build', [
            'json' => [],
        ]);

    }
}
