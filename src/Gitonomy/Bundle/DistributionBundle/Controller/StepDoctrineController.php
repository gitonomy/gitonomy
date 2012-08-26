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
        $step = $this->getSteps()->getStep('database');
        list($code, $message) = $step->testValues($post['parameters']);

        $response = new Response(json_encode(array(
            'code'    => $code,
            'message' => $message
        )));

        $response->headers->set('Content-type', 'application/json');

        return $response;
    }

    protected function getSteps()
    {
        return $this->get('gitonomy_distribution.steps');
    }
}
