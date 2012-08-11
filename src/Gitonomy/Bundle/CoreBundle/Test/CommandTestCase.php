<?php

namespace Gitonomy\Bundle\CoreBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Base class for testing the CLI tools.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
abstract class CommandTestCase extends WebTestCase
{
    /**
     * Runs a command and returns it output
     */
    public function runCommand(Client $client, $command)
    {
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $input = new StringInput($command);
        $output = new StreamOutput(fopen('php://memory', 'w', false));

        $statusCode = $application->run($input, $output);

        rewind($output->getStream());
        $result = stream_get_contents($output->getStream());

        return array($statusCode, $result);
    }
}
