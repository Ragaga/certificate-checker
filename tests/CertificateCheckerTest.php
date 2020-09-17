<?php


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ragaga\CertificateChecker\CertificateChecker;
use Ragaga\CertificateChecker\CheckerFactory;
use Ragaga\CertificateChecker\Checkers\Checker;
use Ragaga\CertificateChecker\Repositories\CASerialNumberRepository;
use Ragaga\CertificateChecker\SignatureData;

class CertificateCheckerTest extends TestCase
{
    /** @var MockObject|CASerialNumberRepository */
    private $repositoryMock;
    /** @var SignatureData */
    private $signatureData;
    /** @var CheckerFactory */
    private $factory;

    public function setUp(): void
    {
        $this->repositoryMock = $this->createMock(CASerialNumberRepository::class);
        $this->signatureData = new SignatureData('', '', 'test');
        $this->factory = new CheckerFactory();
        parent::setUp();
    }

    public function testCreation()
    {

        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        self::assertInstanceOf(CertificateChecker::class, $checker);
    }

    public function testSignature()
    {

        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('verify')->willReturn(true);

            return $mock;
        });

        $checker = new CertificateChecker($this->factory, $this->repositoryMock);

        self::assertTrue($checker->isSignatureValid($this->signatureData));
        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('verify')->willReturn(false);

            return $mock;
        });

        self::assertFalse($checker->isSignatureValid($this->signatureData));
    }

    public function testInn()
    {
        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('getCertificate')->willReturn(['subject' => ['INN' => '111222']]);

            return $mock;
        });

        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        self::assertTrue($checker->isInnValid($this->signatureData, '111222'));

        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('getCertificate')->willReturn(['subject' => ['INN' => '1112221']]);

            return $mock;
        });

        self::assertFalse($checker->isInnValid($this->signatureData, '111222'));
    }

    public function testHeadName()
    {
        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('getCertificate')->willReturn([
                'subject' => [
                    'SN' => 'HEAD',
                    'GN' => 'NAME'
                ]
            ]);

            return $mock;
        });

        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        self::assertTrue($checker->isHeadNameValid($this->signatureData, 'Head Name'));

        $this->factory->bind('test', function () {
            $mock = $this->createMock(Checker::class);
            $mock->method('getCertificate')->willReturn([
                'subject' => [
                    'SN' => 'HEAD',
                    'GN' => 'SURNAME'
                ]
            ]);

            return $mock;
        });

        self::assertFalse($checker->isHeadNameValid($this->signatureData, 'Head Name'));
    }

    public function testSerialNumber()
    {
        $this->repositoryMock->method('exist')->willReturn(true);

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

    public function testCertificate()
    {
        $cert = ['test_cert'];

        $this->factory->bind('test', function () use ($cert) {
            $mock = $this->createMock(Checker::class);
            $mock->method('getCertificate')->willReturn($cert);

            return $mock;
        });

        $checker = new CertificateChecker($this->factory, $this->repositoryMock);
        self::assertEquals($cert, $checker->getCertificate($this->signatureData));
    }
}
