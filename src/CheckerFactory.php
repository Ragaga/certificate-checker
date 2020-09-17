<?php

namespace Ragaga\CertificateChecker;

use Ragaga\CertificateChecker\Checkers\Checker;

class CheckerFactory
{
    private $binds    = [];
    private $checkers = [];

    public function bind(string $name, \Closure $closure)
    {
        $this->binds[$name] = $closure;
    }

    public function getChecker(string $name): Checker
    {
        $checkerClass = $this->binds[$name] ?? null;

        if ($checkerClass === null) {
            throw new \DomainException('Checker is not binded in this name ' . $name);
        }

        $checker = $this->checkers[$name] ?? null;

        return $checker ?: ($this->binds[$name])();
    }
}
