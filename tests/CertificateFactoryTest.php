<?php


use PHPUnit\Framework\TestCase;
use Ragaga\CertificateChecker\CheckerFactory;
use Ragaga\CertificateChecker\Checkers\Checker;

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
}
