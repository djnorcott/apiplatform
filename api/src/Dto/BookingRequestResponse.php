<?php

namespace App\Dto;

class BookingRequestResponse
{
    public ?string $date_from = null;
    public ?string $date_to = null;

    public int $number_of_free_spaces = 0;

    public float $price = 0.0;

    public function __construct(?string $date_from, ?string $date_to, int $number_of_free_spaces, float $price = 0.0)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->number_of_free_spaces = $number_of_free_spaces;
        $this->price = $price;
    }
}
