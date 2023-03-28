<?php

include 'card_repo.php';
include 'card.php';
include 'transaction_repo.php';

class Processor
{
    private $cardRepo;
    private $transactionRepo;

    private $card;
    private $authenticated;

    public function __construct(CardRepo $cardRepo, TransactionRepo $transactionRepo)
    {
        $this->cardRepo = $cardRepo;
        $this->transactionRepo = $transactionRepo;

        $this->setup();
        if ($this->authenticated) {
            $this->printInfo();
            $this->printMutation();
            $this->topUp(200000);
            $this->printInfo();
            $this->printMutation();
        }
    }

    public function setup()
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
            echo "pin benar: " . $result[0]->getPin() . "\n";
            echo "pin input: " . $pin . "\n";
            if ($result[0]->getPin() == $pin) {
                $this->authenticated = true;
                $this->card = $result[0];
            } else {
                echo "\nPin salah\n";
            }
        }
    }

    public function printInfo()
    {
        echo "\n";
        echo "Informasi Kartu\n";
        echo "---------------\n";
        echo "Nomor Kartu : " . $this->card->getCardNumber() . "\n";
        echo "Saldo       : " . number_format($this->card->getBalance(), 0, ",", ".");
    }

    public function topUp(float $amount)
    {
        $currentBalance = $this->card->getBalance();
        $success = $this->card->setBalance($currentBalance + $amount);

        if ($success) {
            echo "Berhasil";
            $this->transactionRepo->add(
                new Transaction($this->card->getCardNumber(), date("Y-m-d, H:i:s") . " | TopUp sebesar " . number_format($currentBalance, 0, ",", "."))
            );
        } else {
            echo "Gagal";
        }
        $this->printInfo();
    }

    public function printMutation()
    {
        echo "\n\n";
        echo "Mutasi Kartu\n";
        echo "------------\n";
        foreach ($this->transactionRepo->whereCardNumber($this->card->getCardNumber()) as $transaction) {
            echo $transaction->desc;
            echo "\n";
        }
    }
}


$transactionRepo = new TransactionRepo();
$listCard = array();

$listCard[] = new ATM("1111111111", "111111", 1000000, "Dimas Saputro", "11111", "BCA-Purwodadi");
$listCard[] = new ATM("2222222222", "222222", 250000, "Widjanarko", "22222", "BCA-Semarang");

$cardRepo = new CardRepo($listCard);

$processor = new Processor($cardRepo, $transactionRepo);

// $card = $cardRepo->whereCardNumber("1111111111");
