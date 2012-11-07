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

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorController extends BaseController
{
    public function showAction(FlattenException $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $statusCode = $exception->getStatusCode();
        $statusText = $exception->getMessage();

        $response = $this->render('GitonomyWebsiteBundle:Error:error.html.twig', array(
            'status_code' => $statusCode,
            'status_text' => $statusText,
        ));
        $response->setStatusCode($statusCode);
        $response->headers->replace($exception->getHeaders());

        return $response;
    }
}
