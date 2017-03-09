<?php

/**
 * User: Bartłomiej Różycki
 * Date: 3/9/17
 * Time: 7:44 PM
 */

namespace NBPGoldBroker\Api;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;
use NBPGoldBroker\Api\Contracts\HttpRequest;

class NBP
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var HttpRequest
     */
    private $client;

    /**
     * @param string $host
     * @param HttpRequest $client
     */
    public function __construct($host, HttpRequest $client)
    {
        $this->host = $host;
        $this->client = $client;
    }

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @return array
     * @throws InvalidArgumentException
     */
    public function getGoldPricesBetween(DateTimeImmutable $start, DateTimeImmutable $end)
    {
        if ($start > $end) {
            throw new InvalidArgumentException("Start date cannot be greater than end date");
        }
        // API limits the range to 367 days, but we want this method
        // to work with greater range, so we must call the api several times
        // and merge the response
        $result = [];
        $tmpStart = DateTimeImmutable::createFromFormat("Y-m-d", $start->format("Y-m-d"));
        $tmpEnd = DateTimeImmutable::createFromFormat("Y-m-d", $end->format("Y-m-d"));
        $rangeLimitInDays = 367;
        $diffInDays = $start->diff($end)->days;
        $rateInterval = new DateInterval("P" . $rangeLimitInDays . "D");
        while ($diffInDays > 0) {
            if ((int)($diffInDays / $rangeLimitInDays)) {
                // At each iteration we create a new range
                // that consists of 367 days
                $tmpEnd = $tmpStart->add($rateInterval);
            } else {
                // Finally we cannot add another 367 days, cause that would
                // exceed the $end, so we just set the tmpEnd to $end
                // to get the final chunk
                $tmpEnd = $end;
            }
            $this->client->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->client->setOption(CURLOPT_URL,
                $this->host . "/cenyzlota/" .
                $tmpStart->format("Y-m-d") . "/" . $tmpEnd->format("Y-m-d")
            );
            $response = json_decode($this->client->execute());
            $result = array_merge($result, (array)$response);
            $this->client->flush();
            $tmpStart = $tmpStart->add($rateInterval);
            $diffInDays -= $rangeLimitInDays;
        }
        return $result;
    }
}