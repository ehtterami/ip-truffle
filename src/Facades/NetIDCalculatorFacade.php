<?php

namespace Ehtterami\IpTruffle\Facades;

use Ehtterami\IpTruffle\Services\NetIDCalculatorService;

class NetIDCalculatorFacade
{
    private NetIDCalculatorService $calculator;

    public function __construct()
    {
        $this->calculator = NetIDCalculatorService::getInstance();
    }

    public function calculate(string $ipAddress, string $subnetMask): array
    {
        return $this->calculator->calculate($ipAddress, $subnetMask);
    }
}