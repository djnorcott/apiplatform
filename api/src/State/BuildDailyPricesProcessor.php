<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\BookingRequestResponse;
use App\Entity\DailyPrice;
use App\Entity\ParkingSpace;
use App\Entity\PricingRule;
use App\Repository\ParkingSpaceRepository;
use App\Repository\PricingRuleRepository;
use Doctrine\ORM\EntityManagerInterface;

class BuildDailyPricesProcessor implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $this->entityManager->wrapInTransaction(function(EntityManagerInterface $em) {
            // Step 1: Delete old prices
            $em->createQueryBuilder()
                ->delete(DailyPrice::class, 'dp')
                ->getQuery()
                ->execute();

            // Step 2: Load pricing rules
            /** @var PricingRule[] $rules */
            $rules = $em->createQueryBuilder()
                ->select('r')
                ->from(PricingRule::class, 'r')
                ->orderBy('r.priority', 'DESC')
                ->getQuery()
                ->getResult();

            if (empty($rules)) {
                return;
            }

            // Step 3: Generate full date range
            $minDate = min(array_map(static fn($r) => $r->getDateFrom(), $rules));
            $maxDate = max(array_map(static fn($r) => $r->getDateTo(), $rules));

            // Turn minDate and maxDate into dates
            $minDate = \DateTime::createFromFormat('Ymd', $minDate);
            $maxDate = \DateTime::createFromFormat('Ymd', $maxDate);

            $period = new \DatePeriod($minDate, new \DateInterval('P1D'), (clone $maxDate)->modify('+1 day'));

            // Step 4: Generate daily prices
            foreach ($period as $date) {
                $formattedDate = $date->format('Ymd');

                $applicableRules = array_filter($rules, static fn(PricingRule $r) => $r->getDateFrom() <= $formattedDate && $r->getDateTo() >= $formattedDate);
                if (empty($applicableRules)) {
                    continue;
                }

                $selectedRule = reset($applicableRules); // already sorted by priority
                $isWeekend = in_array((int) $date->format('w'), [0, 6]);

                $price = $isWeekend ? $selectedRule->getWeekendPrice() : $selectedRule->getWeekdayPrice();

                $dailyPrice = new DailyPrice();
                $dailyPrice->setDate($formattedDate);
                $dailyPrice->setPrice($price);
                $dailyPrice->setPricingRule($selectedRule);

                $em->persist($dailyPrice);
            }

            $em->flush();
        });

        return ['complete' => true];
    }
}
