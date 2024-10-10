<?php

namespace NyroDev\PhpTemplateBundle;

use NyroDev\PhpTemplateBundle\DependencyInjection\Compiler\TemplatingPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NyroDevPhpTemplateBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new TemplatingPass());
    }
}
