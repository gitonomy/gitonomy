<?php

namespace Gitonomy\Bundle\CoreBundle\Job;

use Gitonomy\Bundle\JobBundle\Job\Job;

class UpdateSshKeysJob extends Job
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $em = $this->getService('doctrine')->getManager();

        $file = $this->findFile();

        $keyList = $em->transactional(function ($em) {
            $repository = $em->getRepository('GitonomyCoreBundle:UserSshKey');
            $keyList = $repository->getKeyList();
            $repository->markAllAsInstalled();

            return $keyList;
        });

        // Here we test true, because $em->transactional returns true if the list was an empty list
        if (empty($keyList) || true === $keyList) {
            return;
        }

        $content = $this->generate($keyList);

        file_put_contents($file, $content);
    }

    private function generate($keyList)
    {
        $command = $this->getContainer()->getParameter('gitonomy_core.git.shell_command');
        $output = '';

        foreach ($keyList as $row) {
            $output .= sprintf("command=\"%s %s\" %s\n", $command, $row['username'], $row['content']);
        }

        return $output;
    }

    private function findFile()
    {
        $candidates = array();

        if (isset($_SERVER['HOME'])) {
            $candidates[] = $_SERVER['HOME'].'/.ssh/authorized_keys';
        }

        foreach ($candidates as $file) {
            if (file_exists($file) && is_writable($file)) {
                return $file;
            }
        }

        throw new \RuntimeException(sprintf("Unable to find authorized_keys file in following locations:\n- %s", implode("\n- ", $candidates)));
    }
}
