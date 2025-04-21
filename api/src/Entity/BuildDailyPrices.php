<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\BuildDailyPricesProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/build_daily_prices',
            name: 'build_daily_prices',
            processor: BuildDailyPricesProcessor::class,
        )
    ]
)]
class BuildDailyPrices
{
}
