<?php

use PHPUnit\Framework\TestCase;
use NBPGoldBroker\Api\CurlRequest;
use NBPGoldBroker\Api\NBP;
use NBPGoldBroker\Api\Contracts\HttpRequest;

require '../vendor/autoload.php';
/**
 * @covers NBPTest
 */
class NBPTest extends TestCase
{
    public function testGetGoldPricesBetweenReturnsCorrectResponse() {
        $data = new stdClass();
        $data->data = "2016-01-01";
        $data->cena = "133.22";
        $responseMock = json_encode([$data]);

        $client = $this->createMock(HttpRequest::class);
        $client->expects($this->once())
            ->method("execute")
            ->willReturn($responseMock);

        $url = "client://api.nbp.pl/api";

        $endDate = new DateTimeImmutable();
        $startDate = $endDate->sub(new DateInterval("P1Y"));

        $api = new NBP($url, $client);

        $response = $api->getGoldPricesBetween($startDate, $endDate);

        $this->assertObjectHasAttribute("data", $response[0]);
        $this->assertObjectHasAttribute("cena", $response[0]);
    }
//
    public function testGetGoldPricesThrowsExceptionOnWrongInput() {
        $this->expectException(InvalidArgumentException::class);

        $url = "http://api.nbp.pl/api";

        $startDate = new DateTimeImmutable();
        $endDate = $startDate->sub(new DateInterval("P10Y"));

        $client = new CurlRequest();
        $api = new NBP($url, $client);

        $api->getGoldPricesBetween($startDate, $endDate);
    }

    public function testGetGoldPricesBetweenWorksForRangeGreaterThanOneYear() {
        $data = new stdClass();
        $data->data = "2016-01-01";
        $data->cena = "133.22";
        $responseMock = json_encode([$data]);

        $http = $this->createMock(HttpRequest::class);
        $http->expects($this->exactly(10))
            ->method("execute")
            ->willReturn($responseMock);

        $url = "http://api.nbp.pl/api";

        $endDate = new DateTimeImmutable();
        $startDate = $endDate->sub(new DateInterval("P10Y"));

        $api = new NBP($url, $http);

        $api->getGoldPricesBetween($startDate, $endDate);
    }
}