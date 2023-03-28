<?php

include 'transaction.php';

class TransactionRepo {
    private $listTransaction;

    public function __construct()
    {
        $this->listTransaction = array();
    }

    public function add(Transaction $transaction) {
        $this->listTransaction[] = $transaction;
    }

    public function whereCardNumber(string $cardNumber) : array {
        $result = array();
        foreach ($this->listTransaction as $transaction) {
            if ($transaction->cardNumber == $cardNumber) {
                $result[] = $transaction;
            }
        }
        return $result;
    }
}
