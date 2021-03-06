# Psr7Hmac

An HMAC authentication library built on top of the PSR-7 specification.

[![Build Status](https://travis-ci.org/1ma/Psr7Hmac.svg?branch=master)](https://travis-ci.org/1ma/Psr7Hmac) [![Code Coverage](https://scrutinizer-ci.com/g/1ma/Psr7Hmac/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/1ma/Psr7Hmac/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/1ma/Psr7Hmac/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/1ma/Psr7Hmac/?branch=master) [![Code Climate](https://codeclimate.com/github/1ma/Psr7Hmac/badges/gpa.svg)](https://codeclimate.com/github/1ma/Psr7Hmac) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/8c7c772a-5819-426d-bef9-eb9f2b4a3102/mini.png)](https://insight.sensiolabs.com/projects/8c7c772a-5819-426d-bef9-eb9f2b4a3102)

If you want to build an HMAC-authenticated API based on Symfony check out [UMAPsr7HmacBundle](https://github.com/1ma/UMAPsr7HmacBundle), which
provides a convenient integration of this library with Symfony's [Security Component](http://symfony.com/doc/current/components/security.html).

## Library API

```php
/**
 * @param string $secret
 */
Signer::__construct($secret);

/**
 * @param MessageInterface $message
 *
 * @return MessageInterface
 */
Signer::sign(MessageInterface $message);

/**
 * @param InspectorInterface|null $inspector
 */
Verifier::__construct(InspectorInterface $inspector = null);

/**
 * @param MessageInterface $message
 * @param string           $secret
 *
 * @return bool
 */
Verifier::verify(MessageInterface $message, $secret);
```


## Demo Script

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use UMA\Psr7Hmac\Signer;
use UMA\Psr7Hmac\Verifier;


//// CLIENT SIDE
$psr7request = new \Zend\Diactoros\Request('http://www.example.com/index.html', 'GET');
// GET /index.html HTTP/1.1
// host: www.example.com

$signer = new Signer('secret');

$signedRequest = $signer->sign($psr7request);
// GET /index.html HTTP/1.1
// host: www.example.com
// authorization: HMAC-SHA256 63IQ8RWDbC9p4ipNrkJz0e0UeGiBrR96zkNdujE5cl8=
// signed-headers: host,signed-headers


//// SERVER SIDE
$verifier = new Verifier();

var_dump($verifier->verify($signedRequest, 'secret'));
// true

var_dump($verifier->verify($signedRequest, 'another secret'));
// false

// Headers added after calling sign() do not break the verification, as
// they are not included in the signed-headers list.
var_dump($verifier->verify($signedRequest->withHeader('User-Agent', 'PHP/5.x'), 'secret'));
// true

// Changes made to any chunk of data that was present at the time of the
// signature are still detected, though. In this example a signed header
// is omitted from the Signed-Headers list.
var_dump($verifier->verify($signedRequest->withHeader('Signed-Headers', 'host,signed-headers'), 'secret'));
// false

// The verification also fails if any single part of the message is
// removed altogether after signing it.
var_dump($verifier->verify($signedRequest->withoutHeader('Signed-Headers'), 'secret'));
// false
```


## External Resources

* [[PSR-7] HTTP message interfaces](http://www.php-fig.org/psr/psr-7/)
* [[RFC 2104] HMAC: Keyed-Hashing for Message Authentication](https://tools.ietf.org/html/rfc2104)
* [[RFC 4231] Identifiers and Test Vectors for HMAC-SHA-224, HMAC-SHA-256, HMAC-SHA-384, and HMAC-SHA-512](https://tools.ietf.org/html/rfc4231)
* [[RFC 7230] Hypertext Transfer Protocol (HTTP/1.1): Message Syntax and Routing](https://tools.ietf.org/html/rfc7230)
* [[RFC 7231] Hypertext Transfer Protocol (HTTP/1.1): Semantics and Content](https://tools.ietf.org/html/rfc7231)
* [[RFC 7235] Hypertext Transfer Protocol (HTTP/1.1): Authentication](https://tools.ietf.org/html/rfc7235)


## Disclaimer

The code included in this library has not been reviewed by any cryptographer or security specialist, nor I claim to be one.
If you intend to use in your own projects you are advised to read the documentation, understand the code and report back
any issues you shall find.
