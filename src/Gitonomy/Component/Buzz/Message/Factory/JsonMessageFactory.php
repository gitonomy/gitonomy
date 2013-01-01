<?php

namespace Gitonomy\Component\Buzz\Message\Factory;

use Buzz\Message\Factory\FactoryInterface;
use Buzz\Message\Form\FormRequest;
use Buzz\Message\RequestInterface;
use Buzz\Message\Request;

use Gitonomy\Component\Buzz\Message\JsonResponse;

class JsonMessageFactory implements FactoryInterface
{
    public function createRequest($method = RequestInterface::METHOD_GET, $resource = '/', $host = null)
    {
        return new Request($method, $resource, $host);
    }

    public function createFormRequest($method = RequestInterface::METHOD_POST, $resource = '/', $host = null)
    {
        return new FormRequest($method, $resource, $host);
    }

    public function createResponse()
    {
        return new JsonResponse();
    }
}
