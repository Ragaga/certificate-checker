<?php

namespace Ragaga\CertificateChecker;

use Ragaga\CertificateChecker\Enums\CryptoProvider;

class SignatureData
{
    /** @var string base64 source content */
    private $content;
    /** @var string base64 signature */
    private $signature;
    /** @var string */
    private $providerName;

    public function __construct(
        string $content,
        string $signature,
        string $providerName = CryptoProvider::CRYPTOPRO
    ) {
        $this->content = $content;
        $this->signature = $signature;
        $this->providerName = $providerName;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }
}
