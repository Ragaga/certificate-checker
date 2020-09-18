<?php


namespace Ragaga\CertificateChecker;


class Certificate
{
    /** @var array */
    private $certificate;

    public function __construct(array $certificate)
    {
        if (empty($certificate)) {
            throw new \DomainException('Invalid data for certificate');
        }
        $this->certificate = $certificate;
    }

    public function getInn(): string
    {
        return ltrim($this->certificate['subject']['INN'] ?? '', '0');
    }

    public function getHeadName(): string
    {
        return implode(' ', [
            $this->certificate['subject']['SN'] ?? '',
            $this->certificate['subject']['GN'] ?? ''
        ]);
    }

    public function getSerialNumber(): string
    {
        $ident = $this->certificate['extensions']['authorityKeyIdentifier'] ?? '';
        preg_match('/keyid:([\S]+)/', $ident, $matches);
        $serial = $matches[1] ?? '';

        return str_replace(':', '', $serial);
    }
}
