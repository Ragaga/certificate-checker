<?php

namespace Ragaga\CertificateChecker\Checkers;

use Ragaga\CertificateChecker\Certificate;

class CryptoproChecker implements Checker
{
    protected $checks = [];

    protected function createSignedData(string $source, string $signature)
    {
        $sd = new \CPSignedData();

        $sd->set_ContentEncoding(BASE64_TO_BINARY);
        $sd->set_Content($source);
        $sd->VerifyCades($signature, 1, 1);
        $this->saveSignedData($source, $sd);

        return $sd;
    }

    protected function saveSignedData(string $source, $sd)
    {
        $key = md5($source);
        $this->checks[$key] = $sd;
    }

    public function getSignedData(string $source)
    {
        $key = md5($source);

        return $this->checks[$key] ?? false;
    }

    public function verify(string $source, string $signature): bool
    {
        try {
            $this->createSignedData($source, $signature);

            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function getCertificate(string $source, string $signature)
    {
        $sd = $this->getSignedData($source);
        if (!$sd) {
            $sd = $this->createSignedData($source, $signature);
        }
        $certificates = $sd->get_Certificates();
        $certificate = $certificates->Item(1);
        $beginString = '-----BEGIN CERTIFICATE-----' . "\n";
        $endString = '-----END CERTIFICATE-----' . "\n";

        $export = $certificate->Export(0);
        if (!strpos($export, 'BEGIN')) {
            $certificate = $beginString . PHP_EOL . $export . PHP_EOL . $endString;
        }

        $certificate = openssl_x509_parse($certificate);

        return $this->createCertificate($certificate);
    }

    protected function createCertificate($certs): Certificate
    {
        return new Certificate(openssl_x509_parse($certs));
    }

}
