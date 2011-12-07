<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;

/**
 * Dumps all SSH keys from the project.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class AuthorizedKeysGenerator
{
    /**
     * @var Symfony\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * The shell command to run the git wrapper.
     *
     * @var string
     */
    protected $shellCommand;

    /**
     * Constructor.
     *
     * @param Symfony\Bundle\DoctrineBundle\Registry $doctrine A doctrine registry
     */
    public function __construct(Doctrine $doctrine, $shellCommand)
    {
        $this->doctrine     = $doctrine;
        $this->shellCommand = $shellCommand;
    }

    /**
     * Generates the file.
     *
     * @param boolean $markAsInstalled A boolean indicating if keys should be
     * marked as installed after generating the file content
     *
     * @return string An authorized_keys file content
     */
    public function generate($markAsInstalled = false)
    {
        $em = $this->doctrine->getEntityManager();

        // Fetch list and mark as installed in a transaction
        $keyList = $em->transactional(function ($em) use ($markAsInstalled) {
            $repository = $em->getRepository('GitonomyCoreBundle:UserSshKey');
            $keyList = $repository->getKeyList();
            if ($markAsInstalled) {
                $repository->markAllAsInstalled();
            }

            return $keyList;
        });

        // Generate the file
        $output = '';
        foreach ($keyList as $row) {
            $output .= sprintf("command=\"%s %s\" %s\n", $this->shellCommand, $row['username'], $row['content']);
        }

        return $output;
    }
}
