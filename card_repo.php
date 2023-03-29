<?php

include 'card.php';

class CardRepo
{
    private $listCard;

    public function __construct(array $listCard)
    {
        $this->listCard = $listCard;
    }

    public function whereCardNumber(string $cardNumber): array
    {
        $result = array();

        foreach ($this->listCard as $card) {
            if ($card->getCardNumber() == $cardNumber) {
                $result[] = $card;
                break;
            }
        }

        return $result;
    }

    public function whereAccountNumber(string $accountNumber): array
    {
        $result = array();

        foreach ($this->listCard as $card) {
            if ($card instanceof ATM) {
                if ($card->getAccountNumber() == $accountNumber) {
                    $result[] = $card;
                    break;
                }
            }
        }

        return $result;
    }
}
