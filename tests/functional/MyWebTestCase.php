<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class MyWebTestCase extends WebTestCase
{
    /**
     * @param Response $response
     *
     * @return array
     */
    protected function decodeResponse(Response $response)
    {
        return json_decode($response->getContent(), true);
    }

    /**
     * @param mixed $payload
     *
     * @return string
     */
    protected function encodeToJson($payload)
    {
        return json_encode($payload);
    }
}