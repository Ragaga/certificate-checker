<?php

namespace Ragaga\CertificateChecker\Checkers;


interface Checker
{
    public function verify(string $source, string $signature): bool;

    public function getCertificate(string $source, string $signature);
}
