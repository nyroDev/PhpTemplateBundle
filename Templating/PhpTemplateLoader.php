<?php

namespace NyroDev\PhpTemplateBundle\Templating;

use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Twig\Loader\LoaderInterface as TwigLoaderInterface;

class PhpTemplateLoader implements LoaderInterface
{
    protected $loader;

    protected $paths = [];

    public function setTwigLoader(TwigLoaderInterface $loader)
    {
        $paths = [];
        foreach ($loader->getNamespaces() as $namespace) {
            foreach ($loader->getPaths($namespace) as $path) {
                $paths[] = $path.'/%name%';
                $paths[] = $path.'/%bundle%Bundle/%name%';
            }
        }

        $this->loader = new FilesystemLoader(array_reverse($paths));
    }

    public function load(TemplateReferenceInterface $template)
    {
        return $this->loader->load($template);
    }

    public function isFresh(TemplateReferenceInterface $template, int $time)
    {
        return $this->loader->isFresh($template, $time);
    }
}
