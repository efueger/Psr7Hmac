<?php

namespace UMA\Tests\Psr7Hmac;

use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Signer;
use UMA\Psr7Hmac\Specification;
use UMA\Psr7Hmac\Verifier;

class VerifierTest extends \PHPUnit_Framework_TestCase
{
    const SECRET = '$ecr3t';

    use ReflectionUtil;
    use RequestsProvider;

    /**
     * @var HashCalculator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $calculator;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->calculator = $this->getMockBuilder(HashCalculator::class)
            ->setMethods(['hmac'])
            ->getMock();

        $this->replaceInstanceProperty($this->verifier = new Verifier(), 'calculator', $this->calculator);
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testMissingAuthorizationHeader(RequestInterface $request)
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withoutHeader(Specification::AUTH_HEADER);

        $this->assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testMissingSignedHeadersHeader(RequestInterface $request)
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withoutHeader(Specification::SIGN_HEADER);

        $this->assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }

    /**
     * @dataProvider simplestRequestProvider
     *
     * @param RequestInterface $request
     */
    public function testBadlyFormattedSignature(RequestInterface $request)
    {
        $this->calculator
            ->expects($this->never())
            ->method('hmac');

        $request = (new Signer(self::SECRET))
            ->sign($request)
            ->withHeader(Specification::AUTH_HEADER, Specification::AUTH_PREFIX.'herpder=');

        $this->assertFalse($this->verifier->verify($request, "irrelevant, won't be even checked"));
    }
}
