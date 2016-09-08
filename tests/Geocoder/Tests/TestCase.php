<?php

namespace Geocoder\Tests;

use Geocoder\Model\AddressFactory;
use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use Http\Mock\Client as MockClient;
use Http\Adapter\Guzzle6\Client as GuzzleClient;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param  null|object          $expects
     * @return HttpClient
     */
    protected function getMockAdapter($expects = null)
    {
        if (null === $expects) {
            $expects = $this->once();
        }

        $stream = $this->createMock('Psr\Http\Message\StreamInterface');
        $stream
            ->expects($expects)
            ->method('__toString')
            ->will($this->returnValue(''));

        $client = new MockClient();
        $client->addResponse(new Response(200, [], $stream));

        return $client;
    }

    /**
     * @param $returnValue
     * @return HttpClient
     */
    protected function getMockAdapterReturns($returnValue)
    {
        $client = new MockClient();
        $client->addResponse(new Response(200, [], (string) $returnValue));

        return $client;
    }

    /**
     * Because I was bored to fix the test suite because of
     * a change in a third-party API...
     *
     * @return HttpClient
     */
    protected function getAdapter($apiKey = null)
    {
        return new CachedResponseClient(
            new GuzzleClient(),
            $this->useCache(),
            $apiKey
        );
    }

    /**
     * @return boolean
     */
    protected function useCache()
    {
        return isset($_SERVER['USE_CACHED_RESPONSES']) && true === $_SERVER['USE_CACHED_RESPONSES'];
    }

    protected function createAddress(array $data)
    {
        $addresses = (new AddressFactory())->createFromArray([ $data ]);

        return 0 === count($addresses) ? null : $addresses->first();
    }

    protected function createEmptyAddress()
    {
        return $this->createAddress([]);
    }
}
