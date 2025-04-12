<?php

namespace Ehtterami\IpTruffle\Services;

use InvalidArgumentException;

class TranslatorService
{
    /**
     * Singleton Pattern
     */
    private static ?TranslatorService $instance = null;

    private function __construct()
    {
        // 
    }

    public static function getInstance(): TranslatorService
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // * Class Body

    
    private const MAX_OCTET_VALUE = 255;
    private const BINARY_LENGTH = 8;

    public function translate(array $input): array
    {
        if(isset($input['ip'], $input['mask'])) {
            return [
                'ip' => $this->translateOctets($input['ip']),
                'mask' => $this->translateOctets($input['mask']),
                'default_mask' => $this->getDefaultSubnetMask($input['ip'])
            ];
        }

        return [
            'ip' => $this->translateOctets($input['ip']),
            'default_mask' => $this->getDefaultSubnetMask($input['ip'])
        ];
    }

    public function translateOctets(array $octets): string
    {
        $this->validate($octets);

        return match (true) {
            $this->isDecimalFormat($octets) => $this->toBinaryNotation($octets),
            $this->isBinaryFormat($octets) => $this->toDecimalNotation($octets),
            default => throw new InvalidArgumentException('Invalid octet format')
        };
    }

    private function getDefaultSubnetMask(array $octets): string
    {
        $firstOctet = is_numeric($octets[0]) ? $octets[0] : bindec($octets[0]);

        return match (true) {
            $firstOctet <= 127 => 'Type A: 255.0.0.0',
            $firstOctet <= 191 => 'Type B: 255.255.0.0',
            $firstOctet <= 223 => 'Type C: 255.255.255.0',
            default => '255.255.255.255'
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
        foreach($octets as $octet) {
            if(!is_numeric($octet) || $octet < 0 || $octet > self::MAX_OCTET_VALUE) {
                return false;
            }
        }
        return true;
    }

    private function isBinaryFormat(array $octets): bool
    {
        foreach($octets as $octet) {
            if(!is_string($octet) || !preg_match('/^[01]{8}$/', $octet)) {
                return false;
            }
        }
        return true;
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
