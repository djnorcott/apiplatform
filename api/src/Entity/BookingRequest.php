<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Validator\Constraints as Assert;
use App\State\BookingRequestProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/bookings/request',
            name: 'booking_request',
            processor: BookingRequestProcessor::class,
        )
    ]
)]
class BookingRequest
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{4}\d{2}\d{2}$/',
        message: 'The from_date must be in the format yyyymmdd.'
    )]
    public ?string $from_date = null;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{4}\d{2}\d{2}$/',
        message: 'The to_date must be in the format yyyymmdd.'
    )]
    public ?string $to_date = null;
}
