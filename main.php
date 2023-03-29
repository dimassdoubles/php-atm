<?php

include "atm_machine.php";

$transactionRepo = new TransactionRepo();
$listCard = array();

array_push($listCard, new ATM("1111111111", "111111", 970000, "Jonathan", "11111", "BCA-Purwodadi"));
array_push($listCard, new ATM("2222222222", "222222", 250000, "Widjanarko", "22222", "BCA-Semarang"));
array_push($listCard, new ATM("3333333333", "333333", 100000, "Sudjatmiko", "33333", "BCA-Surabaya"));
array_push($listCard, new ATM("4444444444", "444444", 970000, "Sutsujin", "44444", "BCA-Jakarta"));
array_push($listCard, new ATM("5555555555", "555555", 250000, "Erick", "55555", "BCA-Bandung"));
array_push($listCard, new EMoney("0000000000", "000000", 900000));
array_push($listCard, new EMoney("9999999999", "999999", 100000));

$cardRepo = new CardRepo($listCard);

while (true) {
    $atmMachine = new AtmMachine($cardRepo, $transactionRepo);
}
