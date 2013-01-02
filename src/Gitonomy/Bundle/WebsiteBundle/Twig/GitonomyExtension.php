<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\WebsiteBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Git\Blob;
use Gitonomy\Git\Reference;
use Gitonomy\Git\Tree;

class GitonomyExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'gravatar'           => new \Twig_Function_Method($this, 'getGravatar'),
            'render_blob'        => new \Twig_Function_Method($this, 'renderBlob', array('is_safe' => array('html'))),
            'history_graph_data' => new \Twig_Function_Method($this, 'getHistoryGraphData'),
            'branches_activity'  => new \Twig_Function_Method($this, 'getBranchesActivity'),
        );
    }

    public function getFilters()
    {
        return array(
            'base64_encode' => new \Twig_Filter_Function('base64_encode')
        );
    }

    public function getTests()
    {
        return array(
            'git_tree'     => new \Twig_Test_Method($this, 'isGitTree'),
        );
    }

    public function getGlobals()
    {
        return array(
            'config' => $this->container->get('gitonomy_core.config')
        );
    }

    public function getName()
    {
        return 'gitonomy';
    }

    public function getBranchesActivity(Project $project, $reference = null)
    {
        $repository = $project->getRepository();
        $references = $repository->getReferences();

        if (null === $reference) {
            $reference = $project->getDefaultBranch();
        }

        $against = $references->getBranch(null === $reference ? $project->getDefaultBranch() : $reference);

        foreach ($references->getBranches() as $branch) {
            $logBehind = $repository->getLog($branch->getFullname().'..'.$against->getFullname());
            $logAbove = $repository->getLog($against->getFullname().'..'.$branch->getFullname());

            $rows[] = array(
                'branch'           => $branch,
                'above'            => count($logAbove->getCommits()),
                'behind'           => count($logBehind->getCommits()),
                'lastModification' => $branch->getLastModification(),
            );
        }

        usort($rows, function ($left, $right) {
            return $left['lastModification']->getTimestamp() < $right['lastModification']->getTimestamp();
        });

        return $rows;
    }

    public function getHistoryGraphData(Project $project, $commits)
    {
        $referenceName = function (Reference $reference) {
            return $reference->getName();
        };

        $references = $project->getRepository()->getReferences();
        $convert = function ($commit) use ($project, $references, $referenceName) {
            return array(
                'hash'            => $commit->getHash(),
                'short_message'   => $commit->getShortMessage(),
                'parents'         => $commit->getParentHashes(),
                'tags'            => array_map($referenceName, $references->resolveTags($commit)),
                'branches'        => array_map($referenceName, $references->resolveBranches($commit)),
            );
        };

        return array_map($convert, $commits->getArrayCopy());
    }

    public function getGravatar($email, $size = 50)
    {
        return 'http://www.gravatar.com/avatar/'.md5($email).'?s='.$size;
    }

    public function isGitTree($tree)
    {
        return $tree instanceof Tree;
    }

    public function renderBlob(Blob $blob, $path = null)
    {
        $mime = $blob->getMimetype();

        $ctx = array('blob' => $blob);
        if ($blob->isText()) {
            $tpl = 'GitonomyWebsiteBundle:Blob:codemirror.html.twig';
            $ctx['codemirror_mode'] = $this->getCodeMirrorMode($path);
        } elseif (preg_match("#^image/(png|jpe?g|gif)#", $mime)) {
            $tpl = 'GitonomyWebsiteBundle:Blob:image.html.twig';
        } else {
            $tpl = 'GitonomyWebsiteBundle:Blob:_unknown.html.twig';
        }

        return $this->container->get('twig')->render($tpl, $ctx);
    }

    protected function getCodeMirrorMode($path)
    {
        switch (true) {
            case preg_match('#\.sh$#', $path):
                return 'shell';
            case preg_match('#\.json$#', $path):
                return 'javascript';
            case preg_match('#\.md$#', $path):
                return 'markdown';
            case preg_match('#\.css$#', $path):
                return 'css';
            case preg_match('#\.(xml|xl(if)?f)$#', $path):
                return 'xml';
            case preg_match('#\.(yml|yaml)$#', $path):
                return 'yaml';
            case preg_match('#\.(php|php5|phtml)$#', $path):
                return 'php';
            case preg_match('#\.html(\.twig)?$#', $path):
                return 'htmlmixed';
            default:
                return 'text';
        }
    }
}
