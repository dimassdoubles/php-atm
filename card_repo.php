<?php

class CardRepo {
    private $listCard;

    public function __construct(array $listCard) {
        $this->listCard = $listCard;
    }

    public function whereCardNumber(string $cardNumber) : array {
        $result = array();
        
        foreach ($this->listCard as $card) {
            if ($card->getCardNumber() == $cardNumber) {
                $result[] = $card;
                break;
            }
        }

        return $result;
    }
}