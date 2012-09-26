<?php

namespace Gitonomy\Bundle\DistributionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StepDoctrineController extends Controller
{
    public function testAction(Request $request)
    {
        $post = $request->request->all();
        $step = $this->get('gitonomy_distribution.steps')->getStep('database');

        $response = new Response(json_encode(array(
            'status'  => $step->getStatus($post),
            'message' => $message
        )));

        $response->headers->set('Content-type', 'application/json');

        return $response;
    }
}
