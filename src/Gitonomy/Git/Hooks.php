<?php

namespace Gitonomy\Git;

/**
 * Hooks handler.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Hooks
{
    /**
     * @var Gitonomy\Git\Repository
     */
    protected $repository;

    function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    function has($name)
    {
        return file_exists($this->getPath($name));
    }

    function setSymlink($name, $file)
    {
        $path = $this->getPath($name);
        if (file_exists($path)) {
            throw new \RuntimeException(sprintf('A hook "%s" is already defined', $name));
        }

        symlink($file, $path);
    }

    function set($name, $content)
    {
        $path = $this->getPath($name);
        if (file_exists($path)) {
            throw new \RuntimeException(sprintf('A hook "%s" is already defined', $name));
        }

        file_put_contents($path, $content);
        chmod($path, 0777);
    }

    function remove($name)
    {
        $path = $this->getPath($name);
        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('The hook "%s" was not found'));
        }

        unlink($path);
    }

    protected function getPath($name)
    {
        return $this->repository->getPath().'/hooks/'.$name;
    }
}
