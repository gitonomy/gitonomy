<?php

namespace Gitonomy\Bundle\FrontendBundle\Mail;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 */
class TwigMailGenerator
{
    protected $twig;
    protected $template;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getTemplate($name)
    {
        $this->template = $this->twig->loadTemplate($name);
    }

    public function renderBlock($name, $parameters = array())
    {
        return $this->template->renderBlock($name, $parameters);
    }
}
