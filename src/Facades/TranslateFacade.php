<?php

namespace Ehtterami\IpTruffle\Facades;

use Ehtterami\IpTruffle\Services\ReaderService;
use Ehtterami\IpTruffle\Services\TranslatorService;

class TranslateFacade {
    protected ReaderService $readerService;
    protected TranslatorService $translatorService;

    public function __construct()
    {
        $this->readerService = ReaderService::getInstance();
        $this->translatorService = TranslatorService::getInstance();
    }

    public function translate(string $input): string|array
    {
        $octets = $this->readerService->read($input);
        return $this->translatorService->translate($octets);
    }
}