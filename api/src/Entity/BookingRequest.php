<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\BookingRequestResponse;
use App\State\BookingRequestProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/bookings/request',
            output: BookingRequestResponse::class,
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
        message: 'The date_from must be in the format yyyymmdd.'
    )]
    public ?string $date_from = null;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{4}\d{2}\d{2}$/',
        message: 'The date_to must be in the format yyyymmdd.'
    )]
    public ?string $date_to = null;
}
