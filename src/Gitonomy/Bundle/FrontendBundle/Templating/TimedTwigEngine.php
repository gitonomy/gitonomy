<?php

namespace Gitonomy\Bundle\FrontendBundle\Templating;

use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine as BaseTwigEngine;

class TimedTwigEngine extends BaseTwigEngine
{
    public function loadTemplate($name)
    {
        return $this->load($name);
    }
}
