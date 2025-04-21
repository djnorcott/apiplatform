<?php

namespace App\_OLD;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\Customer;
use App\State\BookingCreateProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/bookings',
            name: 'booking_create',
            processor: BookingCreateProcessor::class,
        )
    ]
)]
class BookingCreate
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

    #[Assert\NotBlank]
    public Customer $customer;
}
