<?php

namespace NyroDev\PhpTemplateBundle\Templating;

use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Twig\Loader\LoaderInterface as TwigLoaderInterface;

class PhpTemplateLoader implements LoaderInterface
{
    protected ?FilesystemLoader $loader = null;

    protected array $paths = [];

    public function setTwigLoader(TwigLoaderInterface $loader)
    {
        $bundlePaths = [];
        $paths = [];

        foreach ($loader->getNamespaces() as $namespace) {
            foreach ($loader->getPaths($namespace) as $path) {
                $paths[] = $path.'/%name%';
                $bundlePaths[] = $path.'/%bundle%Bundle/%name%';
            }
        }

        $this->loader = new FilesystemLoader(array_merge(
            $bundlePaths,
            $paths
        ));
    }

    public function load(TemplateReferenceInterface $template): Storage|false
    {
        return $this->loader->load($template);
    }

    public function isFresh(TemplateReferenceInterface $template, int $time): bool
    {
        return $this->loader->isFresh($template, $time);
    }
}
