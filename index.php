<?php

use NBPGoldBroker\GoldBroker;
use NBPGoldBroker\Api\CurlRequest;
use NBPGoldBroker\Api\NBP;

require __DIR__ . "/vendor/autoload.php";

$budget = 600000;

$endDate = new DateTimeImmutable();
$startDate = $endDate->sub(new DateInterval("P10Y"));

$client = new CurlRequest();
$api = new NBP("http://api.nbp.pl/api", $client);

$prices = $api->getGoldPricesBetween($startDate, $endDate);

$goldBroker = new GoldBroker($prices);

$buy_date = $goldBroker->getBuyDate();
$buy_price = $goldBroker->getBuyPrice();
$sell_date = $goldBroker->getSellDate();
$sell_price = $goldBroker->getSellPrice();

$bought_gold = $budget / $buy_price;
$final_price = $bought_gold * $sell_price;
$profit = $final_price - $budget;

echo '
    Budget: '.number_format($budget, 2, ',', ' ').' PLN<br /><br />
    The Best Time to Buy Gold: '.$buy_date.'<br />
    The Best Time to Sell Gold: '.$sell_date.'<br /><br />
    Maximum possible profit: '.number_format($profit, 2, ',', ' ').' PLN';