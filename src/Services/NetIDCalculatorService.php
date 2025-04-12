<?php

namespace Ehtterami\IpTruffle\Services;

use InvalidArgumentException as Exception;

class NetIDCalculatorService
{
    private static ?NetIDCalculatorService $instance = null;
    private float $startTime;
    private float $memoryUsage;

    private function __construct()
    {
        $this->startTime = microtime(true);
        $this->memoryUsage = memory_get_usage();
    }

    public static function getInstance(): self
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function calculate(string $ipAddress, string $subnetMask): array
    {
        if(!filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new Exception('Invalid IPv4 address format');
        }

        $bin_ip = ip2long($ipAddress);
        $bin_mask = ip2long($subnetMask);

        if($bin_mask === false) {
            throw new Exception('Invalid subnet mask');
        }

        $networkId = $bin_ip & $bin_mask;
        $wildcard = ~$bin_mask;
        $broadcast = $networkId | $wildcard;
        $first = $networkId + 1;
        $last = $networkId - 1;
        $cidr = $this->maskToCidr($subnetMask);

        return [
            'id' => long2ip($networkId),
            'broadcast' => long2ip($broadcast),
            'first' => long2ip($first),
            'last' => long2ip($last),
            'wild' => long2ip($wildcard),
            'cidr' => "/$cidr",
            'time' => sprintf('%.7f', microtime(true) - $this->startTime),
            'memory' => memory_get_usage() - $this->memoryUsage
        ];
    }

    private function maskToCidr(string $subnetMask): int
    {
        return substr_count(decbin(ip2long($subnetMask)), '1');
    }

}