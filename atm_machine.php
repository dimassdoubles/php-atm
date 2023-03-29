<?php

include 'processor.php';

class AtmMachine
{
    private $cardRepo;
    private $transactionRepo;


    public function __construct(CardRepo $cardRepo, TransactionRepo $transactionRepo)
    {
        $this->cardRepo = $cardRepo;
        $this->transactionRepo = $transactionRepo;

        $this->start();
    }

    public function start()
    {
        $card = $this->insertCard();
        if (count($card) > 0) {
            processorFactory($card[0], $this->cardRepo, $this->transactionRepo);
        }
    }

    public function insertCard(): array
    {
        echo "\nSelamat Datang\n";
        echo "--------------\n";
        $cardNumber = readline("Silahkan masukan nomor kartu : ");
        echo "\n";
        $result = $this->cardRepo->whereCardNumber($cardNumber);
        if (!$result) {
            echo "\nKartu tidak valid";
        } else {
            $pin = readline("Silakan masukan nomor pin    : ");
            echo "\n";
            if ($result[0]->getPin() == $pin) {
                return $result;
            } else {
                echo "\nPin salah\n";
            }
        }

        return array();
    }
}
