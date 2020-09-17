<?php


namespace Ragaga\CertificateChecker\Repositories;


interface CASerialNumberRepository
{
    public function exist(string $serialNumber): bool;
}
