<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NyroDev\PhpTemplateBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @deprecated since version 4.3, to be removed in 5.0; use Twig instead.
 */
class TemplatingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('nyrodev.templating.php.templating')) {
            return;
        }

        $refs = [];
        $helpers = [];

        foreach ($container->findTaggedServiceIds('templating.helper', true) as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $helpers[$attributes[0]['alias']] = $id;
                $refs[$id] = new Reference($id);
            }
        }

        if (\count($helpers) > 0) {
            $definition = $container->getDefinition('nyrodev.templating.php.templating');
            $definition->addMethodCall('setHelpers', [$helpers]);

            if ($container->hasDefinition('nyrodev.templating.engine.php.helpers_locator')) {
                $container->getDefinition('nyrodev.templating.engine.php.helpers_locator')->replaceArgument(0, $refs);
            }
        }
    }
}
