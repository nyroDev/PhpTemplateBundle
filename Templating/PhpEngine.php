<?php

namespace NyroDev\PhpTemplateBundle\Templating;

use Psr\Container\ContainerInterface;
use Symfony\Component\Templating\PhpEngine as BasePhpEngine;

use function is_string;

class PhpEngine extends BasePhpEngine
{
    protected ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function setHelpers(array $helpers): void
    {
        foreach ($helpers as $k => $helper) {
            if (is_string($helpers[$k])) {
                $helpers[$k] = $this->container->get($helper);
            }
        }

        parent::addHelpers($helpers);
    }
}
