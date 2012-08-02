<?php

namespace Gitonomy\Bundle\DistributionBundle\Composer;

class ScriptHandler
{
    const REQUIRE_LINE       = 'require_once dirname(__FILE__).\'/SymfonyRequirements.php\';';
    const INSTANCIATION      = 'new SymfonyRequirements();';
    const REQUIREMENTS_CLASS = 'GitonomyRequirements';

    static private $requiredFiles = array(
        'SymfonyRequirements.php',
        'GitonomyRequirements.php'
    );

    public static function installRequirementsFile($event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().', can not install the requirements file.'.PHP_EOL;

            return;
        }

        $checkFile = $appDir.'/check.php';
        if (!file_exists($checkFile)) {
            throw new \RuntimeException('Unable to find check.php in app folder: '.$appDir);
        }

        $content = file_get_contents($checkFile);
        $content = self::injectRequirements($content);
        file_put_contents($checkFile, $content);
    }

    public static function injectRequirements($content)
    {
        $requirePos = strpos($content, self::REQUIRE_LINE);
        if (false === $requirePos) {
            throw new \RuntimeException('Unable to find require position');
        }
        $instanciationPos = strpos($content, self::INSTANCIATION);
        if (false === $instanciationPos) {
            throw new \RuntimeException('Unable to find instanciation position');
        }

        $result = substr($content, 0, $requirePos);
        foreach (self::$requiredFiles as $file) {
            $result .= sprintf('require_once dirname(__FILE__).\'/%s\';%s', $file, "\n");
        }

        $start = $requirePos + strlen(self::REQUIRE_LINE) + 1;
        $end = $instanciationPos - $start;
        $result .= substr($content, $start, $end);

        $result .= 'new '.self::REQUIREMENTS_CLASS.'();';

        $start = $instanciationPos + strlen(self::INSTANCIATION);
        $result .= substr($content, $start);

        return $result;
    }

    protected static function getOptions($event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
            'symfony-web-dir' => 'web',
            'symfony-assets-install' => 'hard'
        ), $event->getComposer()->getPackage()->getExtra());

        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

}
