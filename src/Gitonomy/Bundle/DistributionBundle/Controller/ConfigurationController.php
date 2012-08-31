<?php

namespace Gitonomy\Bundle\DistributionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Yaml\Yaml;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class ConfigurationController extends Controller
{
    public function welcomeAction()
    {
        return $this->render('GitonomyDistributionBundle:Configuration:welcome.html.twig', array(
            'steps'      => $this->getSteps(),
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

        return $this->render($step->getTemplate(), array(
            'steps'      => $steps,
            'parameters' => $this->getParameters(),
            'step'       => $step,
            'form'       => $form->createView()
        ));
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

        return $this->render($step->getTemplate(), array(
            'steps'      => $steps,
            'parameters' => $this->getParameters(),
            'step'       => $step,
            'form'       => $form->createView()
        ));
    }

    protected function getSteps()
    {
        return $this->get('gitonomy_distribution.steps');
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
        $file = $this->getConfigFile();

        $data = Yaml::parse($file);

        return $data['parameters'];
    }

    protected function setParameters(array $parameters)
    {
        $file = $this->getConfigFile();

        file_put_contents($file, Yaml::dump(array('parameters' => $parameters)));
    }

    protected function getConfigFile()
    {
        return $this->container->getParameter('kernel.root_dir').'/config/parameters.yml';
    }
}
