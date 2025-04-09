<?php
 
namespace Ehtterami\IpTruffle\Services;

class ReaderService
{
    private const IPV4_PATTERN = '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';
    private const BINARY_PATTERN = '/^(?:[01]{8}\.){3}[01]{8}$/';

    public function read(string $input): array
    {
        $input = trim($input);

        if($this->isIpv4($input)) {
            return $this->parseIpv4($input);
        }

        if($this->isBinary($input)) {
            return $this->parseBinary($input);
        }

        throw new \InvalidArgumentException('Invalid input format. Must be either an IPv4 address (e.g., 192.168.1.1) or Binary format (e.g., 11110000.00000000.10101010.01010101');
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
        return array_map(fn(string $octet) => bindec($octet), explode('.', $input));
    }
}