<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Component\Buzz\Message\Factory;

use Buzz\Message\Factory\FactoryInterface;
use Buzz\Message\Form\FormRequest;
use Buzz\Message\RequestInterface;
use Buzz\Message\Request;

use Gitonomy\Component\Buzz\Message\JsonResponse;

/**
 * Json Factory for messages
 *
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
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
