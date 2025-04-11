<?php

namespace Ehtterami\IpTruffle\Services;

class SubnetCalculatorService
{
    private const SUBNET_CLASSES = [
        'A' => ['range' => [0, 126], 'mask' => '255.0.0.0'],
        'B' => ['range' => [128, 191], 'mask' => '255.255.0.0'],
        'C' => ['range' => [192, 223], 'mask' => '255.255.255.0'],
        'D' => ['range' => [224, 239], 'mask' => null],
        'E' => ['range' => [240, 255], 'mask' => null],
    ];

    public function calculate(int $hosts): array
    {
        $requiredBits = ceil(log($hosts + 2, 2));
        $borrowedBits = 32 - $requiredBits;
        $totalHosts = pow(2, $requiredBits);

        return [
            'hosts' => $totalHosts - 2,
            'mask' => $this->getCIDRMask($borrowedBits),
            'cidr' => $borrowedBits
        ];
    }

    public function getIPClass(string $ipAddress = '192.168.1.1'): string
    {
        $firstOctet = (int) strtok($ipAddress, '.');

        foreach(self::SUBNET_CLASSES as $class => $profile) {
            if($firstOctet >= $profile['range'][0] && $firstOctet <= $profile['range'][1]) {
                return $class;
            }
        }

        return 'Unknown';
    }

    public function generateIPRange(string $ipAddress = '192.168.1.0', int $cidr): array
    {
        $ipLong = ip2long($ipAddress);
        $netMask = -1 << (32 - $cidr);
        $network = $ipLong & $netMask;

        $numHosts = pow(2, (32 - $cidr)) - 2;

        $ips = [];
        for($i = 1; $i <= $numHosts; $i++) {
            $ips[] = long2ip($network + $i);
        }

        return $ips;
    }

    private function getCIDRMask(int $cidr): string
    {
        $binary = str_pad(str_repeat('1', $cidr), 32, '0');
        $octets = str_split($binary, 8);

        return implode('.', array_map(
            fn($octet) => bindec($octet), $octets
        ));
    }
}