<?php

abstract class Card {
    abstract public function getCardNumber(): string;
    abstract public function getPin(): string;
    abstract public function getBalance(): float;
    abstract public function setBalance(float $balance): bool;
}

class ATM extends Card {
    private $cardNumber;
    private $pin;
    private $balance;

    private $owner;
    private $accountNumber;
    private $bank;

    function __construct(string $cardNumber, string $pin, float $balance, string $owner, string $accountNumber, string $bank) 
    {
        $this->cardNumber = $cardNumber;
        $this->pin = $pin;
        $this->balance = $balance;
        $this->owner = $owner;
        $this->accountNumber = $accountNumber;
        $this->bank = $bank;
    }
    
    public function getCardNumber(): string {
        return $this->cardNumber;
    }

    public function getPin() : string {
        return $this->pin;
    }

    public function getBalance() : float {
        return $this->balance;
    }

    public function getOwner() : string {
        return $this->owner;
    }

    public function getAccountNumber() : string {
        return $this->accountNumber;
    }

    public function getBank() : string {
        return $this->bank;
    }

    public function setBalance(float $balance) : bool {
        if ($balance >= 0) {
            $this->balance = $balance;
            return true;
        } return false;
    }
}

class EMoney extends Card {
    private $cardNumber;
    private $pin;
    private $balance;

    public function __construct(string $cardNumber, string $pin, float $balance) 
    {
        $this->cardNumber = $cardNumber;
        $this->pin = $pin;
        $this->balance = $balance;
    }

    function getCardNumber() : string {
        return $this->cardNumber;
    }

    function getPin() : string {
        return $this->pin;
    }

    function getBalance() : float {
        return $this->balance;
    }

    function setBalance(float $balance) : bool {
        if ($balance >= 0 && $balance <= 1000000) {
            $this->balance = $balance;
            return true;
        } return false;
    }
}
