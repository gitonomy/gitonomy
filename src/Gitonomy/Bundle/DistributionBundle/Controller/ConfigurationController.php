<?php

namespace Gitonomy\Bundle\DistributionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Yaml\Yaml;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;
use Gitonomy\Bundle\DistributionBundle\Installation\StepList;

class ConfigurationController extends Controller
{
    public function welcomeAction()
    {
        return $this->render('GitonomyDistributionBundle:Configuration:welcome.html.twig', array(
            'steps'      => $this->getSteps(),
            'firstStep'  => $this->getSteps()->getFirst(),
            'parameters' => $this->getParameters()
        ));
    }

    public function finishAction()
    {
        return $this->render('GitonomyDistributionBundle:Configuration:finish.html.twig', array(
            'steps'      => $this->getSteps(),
            'parameters' => $this->getParameters()
        ));
    }

    public function stepAction($slug)
    {
        $steps = $this->getSteps();
        try {
            $step = $steps->getStep($slug);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFound(sprintf('Step named "%s" not found', $slug), $e);
        }

        $form = $this->getForm($step);

        return $this->renderStep($steps, $step, $form);
    }

    public function saveStepAction(Request $request, $slug)
    {
        $steps = $this->getSteps();
        try {
            $step = $steps->getStep($slug);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFound(sprintf('Step named "%s" not found', $slug), $e);
        }

        $form = $this->getForm($step);
        $form->bind($request);
        if ($form->isValid()) {
            $this->saveForm($form);

            return $this->redirect($this->generateUrl('gitonomydistribution_configuration_step', array('slug' => $step->getSlug())));
        }

        return $this->renderStep($steps, $step, $form);
    }

    protected function getSteps()
    {
        return $this->get('gitonomy_distribution.steps');
    }

    protected function renderStep(StepList $steps, StepInterface $step, Form $form)
    {
        return $this->render($step->getTemplate(), array(
            'steps'      => $steps,
            'parameters' => $this->getParameters(),
            'step'       => $step,
            'form'       => $form->createView()
        ));
    }

    protected function getForm(StepInterface $step)
    {
        $parameters = $this->getParameters();

        return $this->get('form.factory')->createNamed('parameters', $step->getForm(), $parameters);
    }

    protected function saveForm(Form $form)
    {
        $parameters = $this->getParameters();

        $this->setParameters(array_merge($parameters, $form->getData()));
    }

    protected function getParameters()
    {
        list($dist, $local) = $this->getDistAndLocal();

        return array_merge($dist, $local);
    }

    protected function setParameters(array $parameters)
    {
        list($dist, $local) = $this->getDistAndLocal();

        $newLocal = array_merge($local, $parameters);

        foreach ($newLocal as $key => $value) {
            if (array_key_exists($key, $dist) && $value === $dist[$key]) {
                unset($newLocal[$key]);
            }
        }

        file_put_contents($this->getLocalFile(), Yaml::dump(array('parameters' => $newLocal)));
    }

    protected function getDistAndLocal()
    {
        $dist = $this->getDistributedFile();
        $dist = Yaml::parse(file_get_contents($dist));

        $local = $this->getLocalFile();
        if (file_exists($local)) {
            $local = Yaml::parse(file_get_contents($local));
        } else {
            $local = array();
        }

        $local = isset($local['parameters']) ? $local['parameters'] : array();
        $dist  = isset($dist['parameters']) ? $dist['parameters'] : array();

        return array($dist, $local);
    }

    protected function getDistributedFile()
    {
        return $this->container->getParameter('kernel.root_dir').'/config/parameters_dist.yml';
    }

    protected function getLocalFile()
    {
        return $this->container->getParameter('kernel.root_dir').'/config/parameters.yml';

    }
}
