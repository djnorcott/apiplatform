<?php

namespace App\_OLD;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\Booking;
use App\Entity\Customer;
use App\State\BookingModifyProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/bookings/modify',
            name: 'booking_modify',
            processor: BookingModifyProcessor::class,
        )
    ]
)]
class BookingModify
{
    #[Assert\NotBlank]
    public Booking $booking;

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
