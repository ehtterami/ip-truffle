<?php

namespace Ehtterami\IpTruffle\Services;

class BannerService {
    private const COLORS = [
        'reset' => "\033[0m",
        'yellow' => "\033[1;33m",
        'red' => "\033[1;31m",
    ];

    public function render(): string
    {
        return sprintf(
            "\n%s%s%s\n%s%s%s\n%s%s%s\n",
            self::COLORS['red'],
            $this->getMainLogo(),
            self::COLORS['reset'],
            self::COLORS['yellow'],
            $this->getDivider(),
            self::COLORS['reset'],
            self::COLORS['red'],
            $this->getSignature(),
            self::COLORS['reset'],
        );
    }
    
    private function getMainLogo(): string
    {
        return <<<EOT
        
                     ___ ___  _______          __  __ _     
                    |_ _| _ \/ /_   _| _ _  _ / _|/ _| |___ 
                     | ||  _/ /  | || '_| || |  _|  _| / -_)
                    |___|_|/_/   |_||_|  \_,_|_| |_| |_\___|
                    EOT;
    }

    private function getDivider(): string
    {
        return str_repeat('_', 45);
    }

    private function getSignature(): string
    {
        return <<<EOT
            \t\t\t\tby Ehtterami
        EOT;
    }
}