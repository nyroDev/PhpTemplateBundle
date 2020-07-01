<?php

namespace NyroDev\PhpTemplateBundle\Templating;

use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Twig\Loader\LoaderInterface as TwigLoaderInterface;

class PhpTemplateLoader implements LoaderInterface
{
    const DEFAULT = '_default';

    protected $currentLoader;

    protected $loaders = [];

    protected $paths = [];

    public function setTwigLoader(TwigLoaderInterface $loader)
    {
        foreach ($loader->getNamespaces() as $namespace) {
            $paths = [];
            foreach ($loader->getPaths($namespace) as $path) {
                $paths[] = $path.'/%name%';
            }

            $this->addPaths($namespace, $paths);
        }

        $this->initDefaultLoader();
    }

    public function initDefaultLoader()
    {
        $this->loaders[self::DEFAULT] = new FilesystemLoader($this->paths);
        $this->currentLoader = $this->loaders[self::DEFAULT];
    }

    public function addPaths(string $namespace, array $paths)
    {
        $this->loaders[$namespace] = new FilesystemLoader($paths);
        $this->paths = array_merge($this->paths, $paths);
    }

    public function selectLoader($name)
    {
        $loader = self::DEFAULT;

        $tmp = explode('/', $name);
        if (
            count($tmp) > 1
            && '@' === substr($tmp[0], 0, 1)
            && isset($this->loaders[substr($tmp[0], 1)])
        ) {
            $loader = substr($tmp[0], 1);
            unset($tmp[0]);
            $name = implode('/', $tmp);
        }

        $this->currentLoader = $this->loaders[$loader];

        return $name;
    }

    public function load(TemplateReferenceInterface $template)
    {
        $name = $this->selectLoader($template->getPath());
        $template->set('name', $name);

        return $this->currentLoader->load($template);
    }

    public function isFresh(TemplateReferenceInterface $template, int $time)
    {
        return $this->currentLoader->isFresh($template, $time);
    }
}
