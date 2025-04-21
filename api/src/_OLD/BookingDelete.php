<?php

namespace App\_OLD;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Entity\Customer;
use App\State\BookingDeleteProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/bookings/{id}',
            name: 'booking_delete',
            processor: BookingDeleteProcessor::class,
        )
    ]
)]
class BookingDelete
{
    #[Assert\NotBlank]
    public Customer $customer;
}
