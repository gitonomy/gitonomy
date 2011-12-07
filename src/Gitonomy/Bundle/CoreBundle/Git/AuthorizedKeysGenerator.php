<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;

/**
 * Generated an authorized_keys file for the Gitonomy project.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class AuthorizedKeysGenerator
{
    /**
     * Generates the file.
     *
     * @param array $keyList An array of keys (each element has 2 keys: username
     * and content).
     *
     * @param string $shellCommand The shell command to execute the git
     * wrapper
     *
     * @return string An authorized_keys file content
     */
    public function generate($keyList, $shellCommand)
    {
        $output = '';

        foreach ($keyList as $row) {
            $output .= sprintf("command=\"%s %s\" %s\n", $shellCommand, $row['username'], $row['content']);
        }

        return $output;
    }
}
