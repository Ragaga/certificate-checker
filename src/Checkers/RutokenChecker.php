<?php

namespace Ragaga\CertificateChecker\Checkers;

use Ragaga\CertificateChecker\Certificate;

class RutokenChecker implements Checker
{
    /** @var string */
    private $cryptoproPath;
    /** @var string */
    private $savePath;

    public function __construct(string $cryptoproPath = '/opt/cprocsp/bin/amd64/cryptcp', string $savePath = '/tmp')
    {
        $this->cryptoproPath = $cryptoproPath;
        $this->savePath = $savePath;

        is_dir($this->savePath) || mkdir($this->savePath, 0777, true);
    }

    public function verify(string $source, string $signature): bool
    {
        $suffix = uniqid('', true);
        $sourceFilename = 'rutoken_check' . $suffix;
        $signatureFilename = 'rutoken_check' . $suffix . '.sgn';
        $this->saveToFile($sourceFilename, $source);
        $this->saveToFile($signatureFilename, $signature);
        $commandText = 'cd :savePath && :cryptoproPath -verify :sourceContent -f :signature -detached -nocades';
        $command = strtr($commandText, [
            ':savePath'      => $this->savePath,
            ':cryptoproPath' => $this->cryptoproPath,
            ':sourceContent' => $sourceFilename,
            ':signature'     => $signatureFilename
        ]);
        exec($command, $output, $result);

        return $result === 0;
    }

    public function getCertificate(string $source, string $signature): Certificate
    {
        $beginString = '-----BEGIN PKCS7-----' . "\n";
        $endString = '-----END PKCS7-----' . "\n";
        $signature = $beginString . $signature . $endString;
        openssl_pkcs7_read($signature, $certs);

        return new Certificate(openssl_x509_parse($certs[0] ?? []));
    }

    protected function saveToFile(string $fileName, string $source)
    {
        return file_put_contents($this->savePath . $fileName, base64_decode($source));
    }

}
