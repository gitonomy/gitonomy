<?php

namespace Gitonomy\Bundle\FrontendBundle\Templating;

use Symfony\Bundle\TwigBundle\TwigEngine as BaseTwigEngine;

class TwigEngine extends BaseTwigEngine
{
    public function loadTemplate($name)
    {
        return $this->load($name);
    }
}
