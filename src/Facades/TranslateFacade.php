<?php

namespace Ehtterami\IpTruffle\Facades;

use Ehtterami\IpTruffle\Services\ReaderService;
use Ehtterami\IpTruffle\Services\TranslatorService;

class TranslateFacade {
    protected ReaderService $readerService;
    protected TranslatorService $translatorService;

    public function __construct()
    {
        $this->readerService = new ReaderService();
        $this->translatorService = new TranslatorService();
    }

    public function translate(string $input): string
    {
        $octets = $this->readerService->read($input);
        return $this->translatorService->translate($octets);
    }
}