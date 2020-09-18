<?php


use PHPUnit\Framework\TestCase;
use Ragaga\CertificateChecker\CheckerFactory;
use Ragaga\CertificateChecker\Checkers\Checker;
use Ragaga\CertificateChecker\Checkers\CryptoproChecker;
use Ragaga\CertificateChecker\Checkers\RutokenChecker;
use Ragaga\CertificateChecker\Enums\CryptoProvider;

class CertificateFactoryTest extends TestCase
{
    public function testNotBinded()
    {
        $factory = new CheckerFactory();

        $this->expectException(DomainException::class);
        $factory->getChecker('test');
    }

    public function testBind()
    {
        $mock = $this->createMock(Checker::class);
        $factory = new CheckerFactory();
        $factory->bind('test', function () use ($mock) {
            return $mock;
        });
        $checker = $factory->getChecker('test');
        self::assertEquals($mock, $checker);
    }


    public function testDefaultCheckers()
    {
        $factory = new CheckerFactory();
        self::assertInstanceOf(RutokenChecker::class, $factory->getChecker(CryptoProvider::RUTOKEN));
        self::assertInstanceOf(CryptoproChecker::class, $factory->getChecker(CryptoProvider::CRYPTOPRO));
    }

    public function testClassBindings()
    {
        $factory = new CheckerFactory([
            CryptoProvider::CRYPTOPRO => CryptoproChecker::class,
        ]);
        self::assertInstanceOf(CryptoproChecker::class, $factory->getChecker(CryptoProvider::CRYPTOPRO));
        $this->expectException(DomainException::class);
        $factory->getChecker(CryptoProvider::RUTOKEN);
    }
}
