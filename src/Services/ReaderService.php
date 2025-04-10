<?php
namespace Ehtterami\IpTruffle\Services;

use InvalidArgumentException;

class ReaderService
{
    private const IPV4_PATTERN = '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';
    private const BINARY_PATTERN = '/^(?:[01]{8}\.){3}[01]{8}$/';
    private const IPV4_RANGE_PATTERN = '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(?:\/(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))?$/';

    public function read(string $input): array
    {
        $input = trim($input);

        if($this->isIpv4Range($input)) {
            return $this->parseIpv4Range($input);
        }

        if($this->isIpv4($input)) {
            return ['ip' => $this->parseIpv4($input)];
        }

        if($this->isBinary($input)) {
            return ['ip' => $this->parseBinary($input)];
        }

        throw new \InvalidArgumentException('Invalid input format. Must be either an IPv4 address (e.g., 192.168.1.1) or Binary format (e.g., 11110000.00000000.10101010.01010101');
    }

    private function isIpv4Range(string $input): bool
    {
        return (bool) preg_match(self::IPV4_RANGE_PATTERN, $input);
    }

    private function parseIpv4Range(string $input): array
    {
        [$ip, $mask] = explode('/', $input);

        return [
            'ip' => $this->parseIpv4($ip),
            'mask' => str_contains($mask, '.') ? $this->parseIpv4($mask) : $this->convertCidrToMask((int) $mask)
        ];
    }

    private function convertCidrToMask(int $cidr): array
    {
        $binary = str_pad(str_repeat('1', $cidr), 32, '0');

        return array_map('bindec', str_split($binary, 8));
    }

    private function isIpv4(string $input): bool
    {
        return (bool) preg_match(self::IPV4_PATTERN, $input);
    }

    private function isBinary(string $input): bool
    {
        return (bool) preg_match(self::BINARY_PATTERN, $input);
    }

    private function parseIpv4(string $input): array
    {
        return array_map('intval', explode('.', $input));
    }

    private function parseBinary(string $input): array
    {
        return explode('.', $input);
    }
}