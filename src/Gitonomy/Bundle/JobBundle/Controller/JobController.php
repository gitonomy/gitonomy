<?php

namespace Gitonomy\Bundle\JobBundle\Controller;

use Gitonomy\Bundle\WebsiteBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class JobController extends Controller
{
    public function waitAction(Request $request, $id)
    {
        $status = $this->get('gitonomy.job_manager')->getStatus($id);

        $finished = $request->query->get('finished');
        $redirect = $request->query->get('redirect');

        if ($status['finished']) {
            $this->setFlash('success', $finished);

            return $this->redirect($redirect);
        }

        return $this->render('GitonomyJobBundle:Job:wait.html.twig', array(
            'id' => $id
        ));
    }

    public function statusAction($id)
    {
        return $this->json($this->get('gitonomy.job_manager')->getStatus($id));
    }
}
