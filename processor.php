<?php

include 'card_repo.php';
include 'transaction_repo.php';

class Processor
{
    protected $cardRepo;
    protected $transactionRepo;

    protected $card;
    protected $authenticated = true;
    protected $exit = false;

    public function __construct(CardRepo $cardRepo, TransactionRepo $transactionRepo, Card $card)
    {
        $this->cardRepo = $cardRepo;
        $this->transactionRepo = $transactionRepo;
        $this->card = $card;


        while (!$this->exit) {
            $this->printMenu();
            $option = $this->inputOption();
            $this->exit = $this->selectMenu($option);
        }

        echo "\nTransaksi Selesai\n";
        echo   "-----------------\n\n";
    }

    protected function inputOption(): string
    {
        try {
            $option = readline("\nMasukan pilihan menu : ");
            echo "\n";
            return $option;
        } catch (Exception $e) {
            return $this->inputOption();
        }
    }

    protected function start()
    {
        $this->printMenu();
        $optionExist = false;
        while (!$optionExist) {
            $option = $this->inputOption();
            $this->selectMenu($option);
        }
    }

    protected function printMenu()
    {
        echo "\nMenu Transaksi\n";
        echo "--------------\n";
        echo "0. Keluar\n";
        echo "1. Informasi Kartu\n";
        echo "2. Mutasi\n";
        echo "3. Pembayaran Belanja\n";
    }

    protected function selectMenu(string $option): bool
    {
        $exit = false;
        if ($option == "0") {
            $exit = true;
        } else if ($option == "1") {
            echo "\n";
            echo "Informasi Kartu\n";
            echo "---------------\n";
            $this->printInfo();
        } else if ($option == "2") {
            echo "\n";
            echo "Mutasi Kartu\n";
            echo "------------\n";
            $this->printMutation();
        } else if ($option == "3") {
            echo "\n";
            echo "Pembayaran Belanja\n";
            echo "------------------\n";
            $this->payment();
        } else {
            print("\nPilihan tidak terdaftar\n");
        }
        return $exit;
    }

    protected function printInfo()
    {
        echo "\n";
        echo "Nomor Kartu : " . $this->card->getCardNumber() . "\n";
        echo "Saldo       : " . number_format($this->card->getBalance(), 0, ",", ".") . "\n";
    }

    protected function inputAmount(): float
    {
        try {
            $amount = (float) readline("\nMasukan nominal : ");
            echo "\n";
            if ($amount > 0) {
                return $amount;
            }
            return $this->inputAmount();
        } catch (Exception $e) {
            return $this->inputAmount();
        }
    }

    protected function inputAccountNumber(): string
    {
        try {
            $accountNumber = readline("\nMasukan nomor rekening : ");
            echo "\n";
            return $accountNumber;
        } catch (Exception $e) {
            return $this->inputAccountNumber();
        }
    }

    protected function printMutation()
    {
        foreach ($this->transactionRepo->whereCardNumber($this->card->getCardNumber()) as $transaction) {
            echo $transaction->desc;
            echo "\n";
        }
    }

    protected function authenticate()
    {
        $pin = readline("\nMasukan pin kartu : ");
        echo "\n";
        $this->authenticated = $this->card->getPin() == $pin;
        if (!$this->authenticated) {
            $this->authenticated = false;
            echo "Pin salah\n";
        }
    }

    protected function payment()
    {
        $amount = $this->inputAmount();
        if ($this->card->getBalance() > $amount) {
            $success = $this->card->setBalance($this->card->getBalance() - $amount);
            if ($success) {
                echo "\nBerhasil";
                $this->transactionRepo->add(
                    new Transaction($this->card->getCardNumber(), date("Y-m-d, H:i:s") . " | Pembayaran belanja sebesar " . number_format($amount, 0, ",", "."))
                );
            } else {
                echo "\nGagal\n";
            }
        } else {
            echo "\nGagal\n";
        }
    }
}

class ATMProcessor extends Processor
{
    public function __construct(CardRepo $cardRepo, TransactionRepo $transactionRepo, Card $card)
    {
        parent::__construct($cardRepo, $transactionRepo, $card);
    }

    protected function printMenu()
    {
        parent::printMenu();
        echo "4. Setor Tunai\n";
        echo "5. TopUp E-Money\n";
        echo "6. Transfer\n";
        echo "7. Tarik Tunai\n";
    }

    protected function selectMenu(string $option): bool
    {
        if ($option == "4") {
            echo "\n";
            echo "Setor Tunai\n";
            echo "-----------\n";
            $this->deposit();
        } else if ($option == "5") {
            echo "\n";
            echo "TopUp E-Money\n";
            echo "-------------\n";
            $this->topUp();
        } else if ($option == "6") {
            echo "\n";
            echo "Transfer\n";
            echo "--------\n";
            $this->transfer();
        } else if ($option == "7") {
            echo "\n";
            echo "Tarik Tunai\n";
            echo "-----------\n";
            $this->withdraw();
        } else {
            return parent::selectMenu($option);
        }

        return false;
    }

    protected function printInfo()
    {
        parent::printInfo();
        if ($this->card instanceof ATM) {
            echo "Pemilik     : " . $this->card->getOwner() . "\n";
            echo "Nomor Rek   : " . $this->card->getAccountNumber() . "\n";
            echo "Bank        : " . $this->card->getBank() . "\n";
        }
    }

    protected function withdraw()
    {
        $amount = $this->inputAmount();

        if ($amount < $this->card->getBalance()) {
            $success = $this->card->setBalance($this->card->getBalance() - $amount);
            if ($success) {
                echo "\nBerhasil\n";
                $this->transactionRepo->add(
                    new Transaction($this->card->getCardNumber(), date("Y-m-d, H:i:s") . " | Tarik tunai sebesar " . number_format($amount, 0, ",", "."))
                );
            }
        } else {
            echo "\nGagal\n";
        }
    }

    protected function payment()
    {
        parent::authenticate();
        if ($this->authenticated) {
            parent::payment();
        }
    }

    protected function deposit()
    {
        $amount = $this->inputAmount();
        $currentBalance = $this->card->getBalance();
        $success = $this->card->setBalance($currentBalance + $amount);

        if ($success) {
            echo "\nBerhasil\n";
            $this->transactionRepo->add(
                new Transaction($this->card->getCardNumber(), date("Y-m-d, H:i:s") . " | Setor tunai sebesar " . number_format($amount, 0, ",", "."))
            );
        } else {
            echo "\nGagal\n";
        }
    }

    protected function inputCardNumber(): string
    {
        try {
            $cardNumber = readline("\nMasukan nomor kartu : ");
            echo "\n";
            return $cardNumber;
        } catch (Exception $e) {
            return $this->inputCardNumber();
        }
    }

    protected function topUp()
    {
        $destinatinoCardNumber = $this->inputCardNumber();
        $destination = $this->cardRepo->whereCardNumber($destinatinoCardNumber);

        if (!$destination) {
            echo "\nNomor kartu tidak terdaftar\n";
        } else {
            $amount = $this->inputAmount();
            $destinationBalance = $destination[0]->getBalance();
            $cardBalance = $this->card->getBalance();

            // kirim
            $succesReceive = $destination[0]->setBalance($destinationBalance + $amount);
            $successReduce = $this->card->setBalance($cardBalance - $amount);

            if ($succesReceive && $successReduce) {
                echo "\nBerhasil\n";
                $this->transactionRepo->add(new Transaction($this->card->getCardNumber(), date("Y-m-d, H:i:s") . " | TopUp E-Money sebesar " . number_format($amount, 0, ",", ".")));
                $this->transactionRepo->add(new Transaction($destination[0]->getCardNumber(), date("Y-m-d, H:i:s") . " | TopUp E-Money sebesar " . number_format($amount, 0, ",", ".")));
            } else {
                $destination[0]->setBalance($destinationBalance);
                $this->card->setBalance($cardBalance);
                echo "\nGagal\n";
            }
        }
    }

    protected function transfer()
    {
        $destinationAccountNumber = $this->inputAccountNumber();
        $destination = $this->cardRepo->whereAccountNumber($destinationAccountNumber);

        if (!$destination) {
            echo "\nNomor rekening tidak ditemukan";
        } else {
            $amount = $this->inputAmount();
            
            // mengecek apakah saldo cukup 
            if ($this->card->getBalance() > $amount) {
                $this->card->setBalance($this->card->getBalance() - $amount);
                $destination[0]->setBalance($destination[0]->getBalance() + $amount);

                $this->transactionRepo->add(new Transaction($this->card->getCardNumber(), date("Y-m-d, H:i:s") . " | Kirim sebesar " . number_format($amount, 0, ",", ".")));
                $this->transactionRepo->add(new Transaction($destination[0]->getCardNumber(), date("Y-m-d, H:i:s") . " | Terima sebesar " . number_format($amount, 0, ",", ".")));

                echo "\nBerhasil\n";
            } else {
                echo "\nGagal\n";
            }
        }
    }
}

function processorFactory(Card $card, CardRepo $cardRepo, TransactionRepo $transactionRepo): Processor
{
    if ($card instanceof ATM) {
        return new ATMProcessor($cardRepo, $transactionRepo, $card);
    } else if ($card instanceof EMoney) {
        return new Processor($cardRepo, $transactionRepo, $card);
    }
}
