<?php


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ragaga\CertificateChecker\Certificate;
use Ragaga\CertificateChecker\CertificateChecker;
use Ragaga\CertificateChecker\CheckerFactory;
use Ragaga\CertificateChecker\Checkers\Checker;
use Ragaga\CertificateChecker\Checkers\CryptoproChecker;
use Ragaga\CertificateChecker\Checkers\RutokenChecker;
use Ragaga\CertificateChecker\Enums\CryptoProvider;
use Ragaga\CertificateChecker\Repositories\CASerialNumberRepository;
use Ragaga\CertificateChecker\SignatureData;

class CertificateCheckerTest extends TestCase
{
    const CHECKER_WITH_CERT = 'checkerWithCert';
    /** @var MockObject|CASerialNumberRepository */
    private $repositoryMock;
    /** @var SignatureData */
    private $signatureData;
    /** @var CheckerFactory */
    private $factory;
    /** @var MockObject|Certificate */
    private $certificateMock;

    public function setUp(): void
    {
        $this->repositoryMock = $this->createMock(CASerialNumberRepository::class);
        $this->signatureData = new SignatureData('', '', self::CHECKER_WITH_CERT);
        $this->factory = new CheckerFactory();
        $this->certificateMock = $this->createMock(Certificate::class);
        $this->certificateMock->method('getInn')->willReturn('111222');
        $this->certificateMock->method('getHeadName')->willReturn('Head Name');
        $this->certificateMock->method('getSerialNumber')->willReturn('SerialNumber');
        $this->factory->bind(self::CHECKER_WITH_CERT, function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('getCertificate')->willReturn($this->certificateMock);

            return $mock;
        });
        parent::setUp();
    }

    public function testCreation()
    {
        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        self::assertInstanceOf(CertificateChecker::class, $checker);
    }

    public function testSignature()
    {
        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        $signatureData = new SignatureData('', '', 'test');
        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('verify')->willReturn(true);

            return $mock;
        });
        self::assertTrue($checker->isSignatureValid($signatureData));

        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('verify')->willReturn(false);

            return $mock;
        });
        self::assertFalse($checker->isSignatureValid($signatureData));
    }

    public function testInn()
    {
        $checker = new CertificateChecker($this->factory);
        self::assertTrue($checker->isInnValid($this->signatureData, '111222'));
        self::assertFalse($checker->isInnValid($this->signatureData, '1112221'));
    }

    public function testHeadName()
    {
        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        self::assertTrue($checker->isHeadNameValid($this->signatureData, 'Head Name'));
        self::assertFalse($checker->isHeadNameValid($this->signatureData, 'Heads Name'));
    }

    public function testSerialNumber()
    {
        $this->repositoryMock->method('exist')->willReturn(true);
        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        self::assertTrue($checker->isSerialNumberValid($this->signatureData));
    }

    public function testSerialNumberNotExist()
    {
        $this->repositoryMock->method('exist')->willReturn(false);

        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('getCertificate')->willReturn([
                'extensions' => [
                    'authorityKeyIdentifier' => 'authorityKeyIdentifier',
                ]
            ]);

            return $mock;
        });

        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        self::assertFalse($checker->isSerialNumberValid($this->signatureData));
    }
}
