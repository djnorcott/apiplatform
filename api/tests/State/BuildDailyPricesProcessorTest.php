<?php


namespace App\Tests\State;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\DailyPrice;
use App\Entity\PricingRule;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class BuildDailyPricesProcessorTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    private $client;
    private $entityManager;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        /** Set up some overlapping price rules so the processor has something to work with */
        $priceRules = [
            [
                "name" => "aug 2025 normal",
                "dateFrom" => "20250801",
                "dateTo" => "20250831",
                "weekdayPrice" => "12.50",
                "weekendPrice" => "15.25",
                "priority" => 1
            ],
            [
                "name" => "aug 2025 holiday time",
                "dateFrom" => "20250812",
                "dateTo" => "20250826",
                "weekdayPrice" => "20.00",
                "weekendPrice" => "25.00",
                "priority" => 2
            ],
            [
                "name" => "aug 2025 festival weekend",
                "dateFrom" => "20250822",
                "dateTo" => "20250824",
                "weekdayPrice" => "38.00",
                "weekendPrice" => "45.00",
                "priority" => 3
            ],
        ];

        foreach ($priceRules as $ruleData) {
            $rule = new PricingRule();
            $rule->setName($ruleData['name']);
            $rule->setDateFrom($ruleData['dateFrom']);
            $rule->setDateTo($ruleData['dateTo']);
            $rule->setWeekdayPrice($ruleData['weekdayPrice']);
            $rule->setWeekendPrice($ruleData['weekendPrice']);
            $rule->setPriority($ruleData['priority']);

            $this->entityManager->persist($rule);
            $this->entityManager->flush();
        }
    }


    /**
     * Ensure that when we call the endpoint to build the prices, the expected daily prices are created
     * representing the priorities of the pricing rules we created
     */
    public function testBuiltDailyPrices(): void
    {
        $this->client->request('POST', '/build_daily_prices', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [],
        ]);

        self::assertResponseIsSuccessful();

        $dailyPrices = $this->entityManager->getRepository(DailyPrice::class)->findAll();
        self::assertCount(31, $dailyPrices);

        $actual = [];
        foreach ($dailyPrices as $dailyPrice) {
            $actual[] = [$dailyPrice->getDate() => $dailyPrice->getPrice()];
        }

        self::assertSame([
            # aug 2025 normal
            ['20250801' => 12.5],
            ['20250802' => 15.25],
            ['20250803' => 15.25],
            ['20250804' => 12.5],
            ['20250805' => 12.5],
            ['20250806' => 12.5],
            ['20250807' => 12.5],
            ['20250808' => 12.5],
            ['20250809' => 15.25],
            ['20250810' => 15.25],
            ['20250811' => 12.5],
            # aug 2025 holiday time kicks in
            ['20250812' => 20.0],
            ['20250813' => 20.0],
            ['20250814' => 20.0],
            ['20250815' => 20.0],
            ['20250816' => 25.0],
            ['20250817' => 25.0],
            ['20250818' => 20.0],
            ['20250819' => 20.0],
            ['20250820' => 20.0],
            ['20250821' => 20.0],
            # aug 2025 festival weekend kicks in
            ['20250822' => 38.0],
            ['20250823' => 45.0],
            ['20250824' => 45.0],
            # we drop back to holiday time
            ['20250825' => 20.0],
            ['20250826' => 20.0],
            # we drop back to normal
            ['20250827' => 12.5],
            ['20250828' => 12.5],
            ['20250829' => 12.5],
            ['20250830' => 15.25],
            ['20250831' => 15.25],
        ], $actual);
    }
}
