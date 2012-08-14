<?php

require_once __DIR__.'/vendor/autoload.php';

use Gitonomy\Component\Pagination\PagerAdapterInterface;
use Gitonomy\Component\Pagination\Pager;
use Gitonomy\Git\Repository;
use Gitonomy\Git\Log;

class LogAdapter implements PagerAdapterInterface
{
    private $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function get($offset, $limit)
    {
        $this->log->setOffset($offset);
        $this->log->setLimit($limit);

        return $this->log->getCommits();
    }

    public function count()
    {
        return $this->log->countCommits();
    }
}


$repository = new Repository('/var/www/gitonomy.dev/vendor/symfony/symfony');
$pager = new Pager(new LogAdapter($repository->getLog('master')));

$pager->setPerPage($argv[2]);
$pager->setPage($argv[1]);

echo sprintf('Page %s/%s'."\n", $pager->getPage(), $pager->getPageCount());
foreach ($pager as $commit) {
    echo $commit->getHash().': '.$commit->getShortMessage()."\n";
}
