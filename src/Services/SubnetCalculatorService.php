<?php

namespace Ehtterami\IpTruffle\Services;

use InvalidArgumentException;

class SubnetCalculatorService
{
    /**
     * Singleton Pattern
     */
    private static $instance = null;

    private function __construct()
    {
        $this->memoryUsage = 0.0;
    }

    public static function getInstance(): SubnetCalculatorService
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // * Class Body

    private float $startTime;
    private float $memoryUsage;

    public function calculateSubnet(string $baseIPv4Address, int $requiredHostsCount): array
    {
        $this->startTime = microtime(true);

        $ipParts = explode('.', $baseIPv4Address);
        if(count($ipParts) !== 4) {
            throw new InvalidArgumentException('Invalid IPv4 Address');
        }

        $ipClass = $this->getIPClass($baseIPv4Address);
        $maxHostsCount = $this->getMaxHostsCount($ipClass);
        if($requiredHostsCount > $maxHostsCount) {
            $suggestedIPBase = $this->suggestIPBase($requiredHostsCount);
            $ipClass = $this->getIPClass($suggestedIPBase);
            $baseIPv4Address = $suggestedIPBase;
        }

        $cidr = $this->calculateCidr($requiredHostsCount);
        $subnetMask = long2ip(pow(2, 32) - pow(2, 32 - $cidr));

        $ipLong = ip2long($baseIPv4Address);
        $networkAddressLong = $ipLong & ip2long($subnetMask);
        $broadcastAddressLong = $networkAddressLong + pow(2, 32 - $cidr) - 1;

        $startIP = long2ip($networkAddressLong + 1);
        $endIP = long2ip($broadcastAddressLong - 1);

        $availableHostsCount = pow(2, 32 - $cidr) - 2;

        $endTime = microtime(true);
        $executionTime = $endTime - $this->startTime;
        $memoryUsage = memory_get_usage() - $this->memoryUsage;

        return [
            'required' => $requiredHostsCount,
            'available' => $availableHostsCount,
            'mask' => $subnetMask,
            'cidr' => $cidr,
            'class' => $ipClass,
            'base' => $baseIPv4Address,
            'start' => $startIP,
            'end' => $endIP,
            'memory' => $memoryUsage,
            'time' => $executionTime,
        ];
    }

    private function getIPClass(string $IPv4Address): string
    {
        $firstOctet = (int) explode('.', $IPv4Address)[0];
        if($firstOctet >= 1 && $firstOctet < 126) return 'A';
        if($firstOctet >= 128 && $firstOctet < 191) return 'B';
        if($firstOctet >= 192 && $firstOctet < 223) return 'C';
        if($firstOctet >= 224 && $firstOctet < 239) return 'D';
        return 'E';
    }

    private function getMaxHostsCount(string $IPv4Class): int
    {
        if($IPv4Class === 'A') return pow(2, 24) - 2;
        if($IPv4Class === 'B') return pow(2, 16) - 2;
        if($IPv4Class === 'C') return pow(2, 8) - 2;
        return 0;
    }

    private function suggestIPBase(int $requiredHosts): string
    {
        if($requiredHosts <= pow(2, 8) - 2) return '192.168.1.0';
        if($requiredHosts <= pow(2, 16) - 2) return '172.16.0.0';
        return '10.0.0.0';
    }

    private function calculateCidr(int $requiredHosts): int
    {
        $bits = ceil(log($requiredHosts + 2, 2));
        return 32 - $bits;
    }
}