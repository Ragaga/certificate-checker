<?php


use PHPUnit\Framework\TestCase;

class CertificateTest extends TestCase
{
    public $certificate = '-----BEGIN CERTIFICATE-----' . PHP_EOL .
                          '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' . PHP_EOL .
                          '-----END CERTIFICATE-----';

    public function testCreateCertificate()
    {
        $arrayCertificate = openssl_x509_parse($this->certificate);
        $certificate = new \Ragaga\CertificateChecker\Certificate($arrayCertificate);
        self::assertInstanceOf(\Ragaga\CertificateChecker\Certificate::class, $certificate);

        self::assertEquals('222405349970', $certificate->getInn());
        self::assertEquals('Черных Геннадий Сергеевич', $certificate->getHeadName());
        self::assertEquals('9C76EA41C9A7393687D51EBB836EE26F78318848', $certificate->getAuthorityKeyIdentifier());
    }

    public function testCreateCertificateFail()
    {
        $cert = '';
        $arrayCertificate = openssl_x509_parse($cert);
        $this->expectException(DomainException::class);
        $certificate = new \Ragaga\CertificateChecker\Certificate($arrayCertificate);
    }

    public function testSourceCert()
    {
        $arrayCertificate = openssl_x509_parse($this->certificate);
        $certificate = new \Ragaga\CertificateChecker\Certificate($arrayCertificate);
        self::assertEquals($arrayCertificate, $certificate->getSourceCertificate());
    }
}
