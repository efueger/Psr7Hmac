<?php

namespace UMA\Tests\Psr7Hmac\Factory;

use miBadger\Http\Request;
use miBadger\Http\Response;
use miBadger\Http\Stream;
use miBadger\Http\URI;

class MiBadgerFactory implements FactoryInterface
{
    use StreamHelper;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public static function request($method, $url, array $headers = [], $body = null)
    {
        return new Request($method, new URI($url), Request::DEFAULT_VERSION, $headers, new Stream(self::stream($body)));
    }

    /**
     * {@inheritdoc}
     *
     * @return Response
     */
    public static function response($statusCode, array $headers = [], $body = null)
    {
        return new Response($statusCode, '', Response::DEFAULT_VERSION, $headers, new Stream(self::stream($body)));
    }

    /**
     * {@inheritdoc}
     */
    public static function requestClass()
    {
        return Request::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function responseClass()
    {
        return Response::class;
    }
}
