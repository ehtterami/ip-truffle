<?php

namespace Ehtterami\IpTruffle\Services;

use InvalidArgumentException;

class TranslatorService
{
    private const MAX_OCTET_VALUE = 255;
    private const BINARY_LENGTH = 8;

    public function translate(array $octets): string
    {
        $this->validate($octets);

        return match (true) {
            $this->isDecimalFormat($octets) => $this->toBinaryNotation($octets),
            $this->isBinaryFormat($octets) => $this->toDecimalNotation($octets),
            default => throw new InvalidArgumentException('Invalid octet format')
        };
    }

    private function validate(array $octets): void
    {
        if (count($octets) !== 4) {
            throw new InvalidArgumentException('Invalid octet count. Must be exactly 4 octets.');
        }
    }

    private function isDecimalFormat(array $octets): bool
    {
        return array_reduce(
            $octets,
            fn(bool $carry, mixed $octet): bool => $carry && is_numeric($octet) && $octet >= 0 && $octet <= self::MAX_OCTET_VALUE,
            true
        );
    }

    private function isBinaryFormat(array $octets): bool
    {
        return array_reduce(
            $octets,
            fn(bool $carry, mixed $octet): bool => $carry && is_string($octet) && preg_match('/^[01]{8}$/', (string) $octet),
            true
        );
    }

    private function toDecimalNotation(array $octets): string
    {
        return implode('.', array_map(
            fn($octet): string => (string) bindec($octet),
            $octets
        ));
    }

    private function toBinaryNotation(array $octets): string
    {
        return implode('.', array_map(
            fn($octet): string => str_pad(
                decbin((int) $octet),
                self::BINARY_LENGTH,
                '0',
                STR_PAD_LEFT
            ),
            $octets
        ));
    }
}
