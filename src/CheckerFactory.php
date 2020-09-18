<?php

namespace Ragaga\CertificateChecker;

use Ragaga\CertificateChecker\Checkers\Checker;
use Ragaga\CertificateChecker\Checkers\CryptoproChecker;
use Ragaga\CertificateChecker\Checkers\RutokenChecker;
use Ragaga\CertificateChecker\Enums\CryptoProvider;

class CheckerFactory
{
    private $binds    = [];
    private $checkers = [];

    public function __construct($binds = [])
    {
        $binds = empty($binds) ? $this->getDefaultBinds() : $binds;
        foreach ($binds as $name => $checker) {
            $this->binds[$name] = $checker;
        }
    }

    public function bind(string $name, $checker)
    {
        $this->binds[$name] = $checker;
    }

    public function getChecker(string $name): Checker
    {
        $checkerClass = $this->binds[$name] ?? null;

        if ($checkerClass === null) {
            throw new \DomainException('Checker is not binded in this name ' . $name);
        }

        $checker = $this->checkers[$name] ?? null;

        if ($checker) {
            return $checker;
        }

        $creator = $this->binds[$name];

        if (is_callable($creator)) {
            return $creator();
        }

        if (!class_exists($creator)) {
            throw new \DomainException('Class does not exists ' . $creator);
        }

        return new $creator;
    }

    public function getDefaultBinds()
    {
        return [
            CryptoProvider::CRYPTOPRO => CryptoproChecker::class,
            CryptoProvider::RUTOKEN   => RutokenChecker::class,
        ];
    }
}
