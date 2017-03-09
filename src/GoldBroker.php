<?php
/**
 * User: Bartłomiej Różycki
 * Date: 3/9/17
 * Time: 7:53 PM
 */

namespace NBPGoldBroker;

use InvalidArgumentException;
/**
 * Class GoldBroker
 * @package NBPGoldBroker
 */
class GoldBroker
{
    /**
     * @var array
     */
    private $prices;

    /**
     * @var string
     */
    private $buy_date;

    /**
     * @var float
     */
    private $buy_price = 0;

    /**
     * @var string
     */
    private $sell_date;

    /**
     * @var float
     */
    private $sell_price = 0;

    /**
     * @var float
     */
    private $profit = 0;

    /**
     * GoldBroker constructor.
     * @param array $prices
     */
    public function __construct(array $prices)
    {
        $this->prices = $prices;
        $this->analyzePriceTable();
    }

    /**
     * @return string
     */
    public function getBuyDate()
    {
        return $this->buy_date;
    }

    /**
     * @return float
     */
    public function getBuyPrice()
    {
        return $this->buy_price;
    }

    /**
     * @return string
     */
    public function getSellDate()
    {
        return $this->sell_date;
    }

    /**
     * @return float
     */
    public function getSellPrice()
    {
        return $this->sell_price;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * @return bool
     */
    private function analyzePriceTable()
    {
        //prices table has to have minimum two days
        if(sizeof($this->prices) < 2)
            throw new InvalidArgumentException("Price table has to have minimum two days");

        $maxCur = 0;
        $maxSoFar = 0;
        $lowSoFar = $this->prices[0]->cena;

        //loop finds maximum profit
        for($i = 1; $i < sizeof($this->prices); $i++) {
            $maxCur = max(0, $maxCur += $this->prices[$i]->cena - $this->prices[$i-1]->cena);

            $maxSoFar = max($maxCur, $maxSoFar);

            if($lowSoFar > $this->prices[$i-1]->cena) {
                $lowSoFar = $this->prices[$i - 1]->cena;
                $lowObject = $this->prices[$i - 1];
            }

            if($maxSoFar == $maxCur){
                $pricesbest = $this->prices[$i];
                $priceslow = $lowObject;
            }
        }

        if(isset($priceslow) && isset($pricesbest))
        {
            $this->buy_date = $priceslow->data;
            $this->buy_price = $priceslow->cena;
            $this->sell_date = $pricesbest->data;
            $this->sell_price = $pricesbest->cena;
            $this->profit = $maxSoFar;
        }
    }
}