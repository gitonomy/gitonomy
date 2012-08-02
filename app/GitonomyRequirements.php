<?php

class GitonomyRequirements extends SymfonyRequirements
{
    const GIT_VERSION = '1.7';

    public function __construct()
    {
        parent::__construct();
        $installedPhpVersion = phpversion();

        list($gitInstalled, $gitVersion) = $this->findGit();

        $this->addRequirement(
            $gitInstalled,
            'git is installed on system',
            'No git command was found in environment',
            'Install git',
            'Install git'
        );

        if ($gitInstalled) {
            $this->addRequirement(
                version_compare(self::GIT_VERSION, $gitVersion),
                sprintf('git version (must be above %s)', self::GIT_VERSION),
                sprintf('The version is not correct: installed version is %s, needed is %s', $gitVersion, self::GIT_VERSION),
                sprintf('You need to upgrade git to at least version %s', self::GIT_VERSION),
                sprintf('You need to upgrade git to at least version %s', self::GIT_VERSION)
            );
        }
    }

    protected function findGit()
    {
        $proc = proc_open('git --version',
            array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            ), $pipes);

        $res = preg_match('/^git version ([0-9\.]+)\n$/', stream_get_contents($pipes[1]), $vars);
        $rtn = proc_close($proc);
        if (!$res) {
            return array(false, null);
        }
        $version = $vars[1];

        return array(true, $version);
    }
}
