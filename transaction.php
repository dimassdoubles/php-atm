<?php

class Transaction {
    public $cardNumber;
    public $desc;

    public function __construct(string $cardNumber, string $desc)
    {
        $this->cardNumber = $cardNumber;
        $this->desc = $desc;
    }
};