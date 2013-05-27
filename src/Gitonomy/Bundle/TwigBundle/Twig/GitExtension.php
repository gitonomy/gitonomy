<?php

namespace Gitonomy\Bundle\TwigBundle\Twig;

use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Revision;
use Gitonomy\Git\Reference\Tag;
use Gitonomy\Git\Reference\Branch;
use Gitonomy\Git\Reference\Stash;
use Gitonomy\Git\Blob;
use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;
use Gitonomy\Git\Log;
use Gitonomy\Git\Tree;

use Gitonomy\Bundle\TwigBundle\Git\Repository;
use Gitonomy\Bundle\TwigBundle\Routing\GitUrlGeneratorInterface;
use Gitonomy\Bundle\TwigBundle\Twig\TokenParser\GitThemeTokenParser;

class GitExtension extends \Twig_Extension
{
    private $urlGenerator;
    private $themes;

    public function __construct(GitUrlGeneratorInterface $urlGenerator, array $themes = array())
    {
        $this->urlGenerator = $urlGenerator;
        $this->themes       = $themes;
    }

    public function getTokenParsers()
    {
        return array(
            // {% git_theme "my_themes.html.twig" %}
            new GitThemeTokenParser(),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('git_author',            array($this, 'renderAuthor'),           array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_blob',              array($this, 'renderBlob'),             array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_branches',          array($this, 'renderBranches'),         array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_commit_header',     array($this, 'renderCommitHeader'),     array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_diff',              array($this, 'renderDiff'),             array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_log',               array($this, 'renderLog'),              array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_tree',              array($this, 'renderTree'),             array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_log_rows',          array($this, 'renderLogRows'),          array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_status',            array($this, 'renderStatus'),           array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_render',            array($this, 'renderBlock'),            array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_repository_name',   array($this, 'renderRepositoryName'),   array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('git_tags',              array($this, 'renderTags'),             array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_url',               array($this, 'getUrl')),
        );
    }

    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('git_blob', function ($blob) { return $blob instanceof Blob; }),
            new \Twig_SimpleTest('git_commit', function ($commit) { return $commit instanceof Commit; }),
            new \Twig_SimpleTest('git_log', function ($log) { return $log instanceof Log; }),
            new \Twig_SimpleTest('git_tag', function ($tag) { return $tag instanceof Tag; }),
            new \Twig_SimpleTest('git_branch', function ($branch) { return $branch instanceof Branch; }),
            new \Twig_SimpleTest('git_stash', function ($stash) { return $stash instanceof Stash; }),
            new \Twig_SimpleTest('git_tree', function ($tree) { return $tree instanceof Tree; })
        );
    }

    public function renderRepositoryName($value)
    {
        if ($value instanceof Commit) {
            $repository = $value->getRepository();
        } elseif ($value instanceof Repository) {
            $repository = $value;
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported type for Repository name: %s', is_object($value) ? get_class($value) : gettype($value)));
        }

        return $repository->getName();
    }

    public function getUrl($value, array $options = array())
    {
        if (isset($options['path'])) {
            return $this->urlGenerator->generateTreeUrl($value, $options['path']);
        }

        if ($value instanceof Commit) {
            return $this->urlGenerator->generateCommitUrl($value);
        } elseif ($value instanceof Reference) {
            return $this->urlGenerator->generateReferenceUrl($value);
        }

        throw new \InvalidArgumentException(sprintf('Unsupported type for URL generation: %s. Expected a Commit, Reference or Revision', is_object($value) ? get_class($value) : gettype($value)));
    }

    public function renderCommitHeader(\Twig_Environment $env, Commit $commit)
    {
        return $this->renderBlock($env, 'commit_header', array(
            'commit' => $commit,
        ));
    }

    public function renderStatus(\Twig_Environment $env, Repository $repository)
    {
        $wc = $repository->getWorkingCopy();

        return $this->renderBlock($env, 'status', array(
            'diff_staged'  => $wc->getDiffStaged(),
            'diff_pending' => $wc->getDiffPending()
        ));
    }

    public function renderLog(\Twig_Environment $env, Log $log, array $options = array())
    {
        $options = array_merge(array(
            'query_url' => null,
            'per_page'  => 20
        ), $options);

        return $this->renderBlock($env, 'log', array(
            'log'       => $log,
            'query_url' => $options['query_url'],
            'per_page'  => $options['per_page']
        ));
    }

    public function renderLogRows(\Twig_Environment $env, Log $log, array $options = array())
    {
        return $this->renderBlock($env, 'log_rows', array(
            'log' => $log
        ));
    }

    public function renderDiff(\Twig_Environment $env, Diff $diff, array $options = array())
    {
        return $this->renderBlock($env, 'diff', array(
            'diff' => $diff
        ));
    }

    public function renderBranches(\Twig_Environment $env, Repository $repository, array $options = array())
    {
        $options = array_merge(array('local_only' => false), $options);

        if (!$options['local_only']) {
            $branches = $repository->getReferences()->getBranches();
        } else {
            $branches = $repository->getReferences()->getLocalBranches();
        }

        return $this->renderBlock($env, 'branches', array(
            'branches' => $branches,
        ));
    }

    public function renderTags(\Twig_Environment $env, Repository $repository)
    {
        return $this->renderBlock($env, 'tags', array(
            'tags' => $repository->getReferences()->getTags(),
        ));
    }

    public function renderAuthor(\Twig_Environment $env, Commit $commit, array $options = array())
    {
        $options = array_merge(array(
            'size' => 15
        ), $options);

        return $this->renderBlock($env, 'author', array(
            'name'      => $commit->getAuthorName(),
            'size'      => $options['size'],
            'email'     => $commit->getAuthorEmail(),
            'email_md5' => md5($commit->getAuthorEmail())
        ));
    }

    public function renderTree(\Twig_Environment $env, Tree $tree, Revision $revision, $path = '')
    {
        return $this->renderBlock($env, 'tree', array(
            'tree'        => $tree,
            'parent_path' => substr($path, 0, strrpos($path, '/')),
            'path'        => $path,
            'revision'    => $revision
        ));
    }

    public function renderBlob($env, Blob $blob)
    {
        if ($blob->isText()) {
            $block = 'blob_text';
        } else {
            $mime = $blob->getMimetype();
            if (preg_match("#^image/(png|jpe?g|gif)#", $mime)) {
                $block = 'blob_image';
            } else {
                $block = 'blob_binary';
            }
        }

        return $this->renderBlock($env, $block, array('blob' => $blob));
    }

    public function addThemes($themes)
    {
        $themes = reset($themes);
        $themes = is_array($themes) ? $themes : array($themes);
        $this->themes = array_merge($themes, $this->themes);
    }

    public function renderBlock(\Twig_Environment $env, $block, $parameters = array())
    {
        foreach ($this->themes as $theme) {
            if ($theme instanceof \Twig_Template) {
                $template = $theme;
            } else {
                $template =  $env->loadTemplate($theme);
            }
            if ($template->hasBlock($block)) {
                return $this->renderTemplateBlock($env, $template, $block, $parameters);
            }
        }

        throw new \InvalidArgumentException('Unable to find block '.$block);
    }

    private function renderTemplateBlock(\Twig_Environment $env, \Twig_Template $template, $block, array $context = array())
    {
        $context = $env->mergeGlobals($context);
        $level = ob_get_level();
        ob_start();
        try {
            $rendered = $template->renderBlock($block, $context);
            ob_end_clean();

            return $rendered;
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'git';
    }
}
