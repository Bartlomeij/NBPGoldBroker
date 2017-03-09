<?php

namespace NBPGoldBroker\Api;

use NBPGoldBroker\Api\Contracts\HttpRequest;

class CurlRequest implements HttpRequest
{
    private $handle = null;

    public function __construct()
    {
        $this->handle = curl_init();
    }

    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute()
    {
        return curl_exec($this->handle);
    }

    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    public function flush()
    {
        curl_close($this->handle);
        $this->handle = curl_init();
    }
}
