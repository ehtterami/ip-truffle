<?php

namespace Ehtterami\IpTruffle\Facades;

use Ehtterami\IpTruffle\Services\SubnetCalculatorService;

class SubnetCalculatorFacade
{
    private SubnetCalculatorService $subnetCalculatorService;

    public function __construct()
    {
        $this->subnetCalculatorService = SubnetCalculatorService::getInstance();
    }

    public function calculate(string $baseIPv4Address, int $requiredHostsCount): array
    {
        return $this->subnetCalculatorService->calculateSubnet($baseIPv4Address, $requiredHostsCount);
    }
}