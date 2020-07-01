<?php

namespace NyroDev\PhpTemplateBundle\Templating;

use Psr\Container\ContainerInterface;
use Symfony\Component\Templating\PhpEngine as BasePhpEngine;

class PhpEngine extends BasePhpEngine
{
    protected $container;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setHelpers(array $helpers)
    {
        foreach ($helpers as $k => $helper) {
            if (\is_string($helpers[$k])) {
                $helpers[$k] = $this->container->get($helper);
            }
        }

        parent::addHelpers($helpers);
    }
}
