<?php

namespace Gitonomy\QA;

use Symfony\Component\Yaml\Yaml;

class KernelFactory
{
    protected $appDir;

    public function __construct($appDir)
    {
        $this->appDir = $appDir;
    }

    public function run(\Closure $callback)
    {
        require_once $this->appDir.'/AppKernel.php';

        $exception = null;
        $app = new \AppKernel('prod', false);
        $app->boot();
        try {
            $result = $callback($app);
        } catch (\Exception $e) {
            $exception = $e;
        }

        $app->shutdown();

        if ($exception) {
            throw $exception;
        }

        return $result;
    }
}
