<?php

namespace UMA\Psr7Hmac;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use UMA\Psr7Hmac\Internal\HashCalculator;
use UMA\Psr7Hmac\Internal\MessageSerializer;

class Signer
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var HashCalculator
     */
    private $calculator;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
        $this->calculator = new HashCalculator();
    }

    /**
     * @param MessageInterface $message
     *
     * @return MessageInterface The signed message.
     *
     * @throws \InvalidArgumentException When $message is an implementation of
     *                                   MessageInterface that cannot be
     *                                   serialized and thus neither signed.
     */
    public function sign(MessageInterface $message)
    {
        $serialization = MessageSerializer::serialize(
            $preSignedMessage = $this->withSignedHeadersHeader($message)
        );

        return $preSignedMessage->withHeader(
            Specification::AUTH_HEADER,
            Specification::AUTH_PREFIX.$this->calculator->hmac($serialization, $this->secret)
        );
    }

    /**
     * @param MessageInterface $message
     *
     * @return MessageInterface
     */
    private function withSignedHeadersHeader(MessageInterface $message)
    {
        $headers = array_keys(array_change_key_case($message->getHeaders(), CASE_LOWER));
        array_push($headers, mb_strtolower(Specification::SIGN_HEADER));

        // Some of the tested RequestInterface implementations do not include
        // the Host header in $message->getHeaders(), so it is explicitly set when needed
        if ($message instanceof RequestInterface && !in_array('host', $headers)) {
            array_push($headers, 'host');
        }

        // There is no guarantee about the order of the headers returned by
        // $message->getHeaders(), so they are explicitly sorted in order
        // to produce the exact same string regardless of the underlying implementation
        sort($headers);

        return $message->withHeader(Specification::SIGN_HEADER, implode(',', $headers));
    }
}
