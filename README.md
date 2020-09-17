# PHP ^7.2 CryptoPro/Rutoken Certificate checker
Cryptopro/Rutoken certificate checker

Usage: 
```php
use Ragaga\CertificateChecker\CertificateChecker;
use Ragaga\CertificateChecker\CheckerFactory;
use Ragaga\CertificateChecker\Enums\CryptoProvider;
use Ragaga\CertificateChecker\Checkers\RutokenChecker;
use Ragaga\CertificateChecker\Checkers\CryptoproChecker;
use Ragaga\CertificateChecker\SignatureData;

$factory = new CheckerFactory();
$factory->bind(CryptoProvider::CRYPTOPRO, function(){
    return new CryptoproChecker();
});

$factory->bind(CryptoProvider::RUTOKEN, function(){
    return new RutokenChecker();
});

$signatureData = new SignatureData('source', 'signature', CryptoProvider::RUTOKEN);

$checker = new CertificateChecker($factory);

$signatureValid = $checker->isSignatureValid($signatureData);
```
