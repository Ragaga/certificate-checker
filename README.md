# PHP ^7.2 CryptoPro/Rutoken Certificate checker
Installation:
```
composer require ragaga/certificate-checker
```
or add to composer.json in require
```
"ragaga/certificate/checker": "^1.0"
```


##Usage:

```php
use Ragaga\CertificateChecker\CertificateChecker;
use Ragaga\CertificateChecker\CheckerFactory;
use Ragaga\CertificateChecker\Enums\CryptoProvider;
use Ragaga\CertificateChecker\Checkers\RutokenChecker;
use Ragaga\CertificateChecker\Checkers\CryptoproChecker;
use Ragaga\CertificateChecker\SignatureData;
```
Variations of factory creation

* Default
```php
$factory = new CheckerFactory();
```
* Customize on creation
```php
$factory = new CheckerFactory([
  CryptoProvider::CRYPTOPRO => CryptoproChecker::class,
  CryptoProvider::RUTOKEN =>  function(){
      $tmpDir = '/tmp/';
      $cryptoproPath = '/opt/cprocsp/bin/amd64/cryptcp';
      return new RutokenChecker($cryptoproPath, $tmpDir);
  }
]);
```
* Rebound after creation
```php
$factory = new CheckerFactory();
$factory->bind(CryptoProvider::RUTOKEN, function(){
   $tmpDir = '/tmp/';
   $cryptoproPath = '/opt/cprocsp/bin/amd64/cryptcp';
   return new RutokenChecker($cryptoproPath, $tmpDir);
});
```
## Check signature
```php
$signatureData = new SignatureData('source', 'signature', CryptoProvider::RUTOKEN);

$checker = new CertificateChecker($factory);

$signatureValid = $checker->isSignatureValid($signatureData);
```
