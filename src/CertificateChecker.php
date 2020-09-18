<?php

namespace Ragaga\CertificateChecker;


use Ragaga\CertificateChecker\Repositories\CASerialNumberRepository;

class CertificateChecker
{
    /** @var CheckerFactory */
    private $factory;
    /** @var CASerialNumberRepository */
    private $CASerialNumberRepository;

    public function __construct(
        CheckerFactory $factory,
        CASerialNumberRepository $CASerialNumberRepository = null
    ) {
        $this->factory = $factory;
        $this->CASerialNumberRepository = $CASerialNumberRepository;
    }

    public function isSignatureValid(SignatureData $signatureData): bool
    {
        $checker = $this->factory->getChecker($signatureData->getProviderName());

        return $checker->verify($signatureData->getContent(), $signatureData->getSignature());
    }

    public function isInnValid(SignatureData $signatureData, string $inn): bool
    {
        $certificate = $this->getCertificate($signatureData);
        if (!$certificate) {
            throw new \DomainException('Сертификат не найден');
        }

        return $certificate->getInn() === $inn;
    }

    public function isHeadNameValid(SignatureData $signatureData, string $headName): bool
    {
        $certificate = $this->getCertificate($signatureData);

        return mb_strtolower($certificate->getHeadName()) === mb_strtolower($headName);
    }

    public function isSerialNumberValid(SignatureData $signatureData): bool
    {
        if (!$this->CASerialNumberRepository) {
            throw new \DomainException('Serial Number Repository is not set');
        }
        $certificate = $this->getCertificate($signatureData);
        return $this->CASerialNumberRepository->exist($certificate->getSerialNumber());
    }

    public function getCertificate(SignatureData $signatureData): Certificate
    {
        $checker = $this->factory->getChecker($signatureData->getProviderName());

        return $checker->getCertificate($signatureData->getContent(), $signatureData->getSignature());
    }
}
