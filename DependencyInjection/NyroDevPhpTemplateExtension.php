<?php

namespace NyroDev\PhpTemplateBundle\DependencyInjection;

use NyroDev\PhpTemplateBundle\DependencyInjection\Compiler\TemplatingPass;
use NyroDev\PhpTemplateBundle\Helper\HelperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class NyroDevPhpTemplateExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../config'));
        $loader->load('services.yaml');

        $loaderXml = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../config'));
        $loaderXml->load('templating_php.php');

        $container->registerForAutoconfiguration(HelperInterface::class)
            ->addTag(TemplatingPass::TEMPLATING_HELPER_TAG);
    }
}
