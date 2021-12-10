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

use NyroDev\PhpTemplateBundle\Helper\AssetsHelper;
use NyroDev\PhpTemplateBundle\Helper\FormHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TemplatingPass implements CompilerPassInterface
{
    public const TEMPLATING_HELPER_TAG = 'templating.helper';

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('nyrodev.templating.php.templating')) {
            $refs = [];
            $helpers = [];

            foreach ($container->findTaggedServiceIds(self::TEMPLATING_HELPER_TAG, true) as $id => $attributes) {
                if (isset($attributes[0]['alias'])) {
                    $helpers[$attributes[0]['alias']] = $id;
                    $refs[$id] = new Reference($id);
                } elseif (is_callable($id.'::getAlias')) {
                    $helpers[call_user_func($id.'::getAlias')] = $id;
                    $refs[$id] = new Reference($id);
                } else {
                    throw new \Exception('Tag '.self::TEMPLATING_HELPER_TAG.' found, not alias provided');
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

        if ($container->hasDefinition('assets.packages')) {
            $definitionAssets = new Definition(AssetsHelper::class);
            $definitionAssets->addTag(self::TEMPLATING_HELPER_TAG, ['alias' => 'assets']);
            $container->setDefinition('nyrodev.templating.helper.assets', $definitionAssets);
        }

        if ($container->hasDefinition('twig.form.renderer')) {
            $definitionForm = new Definition(FormHelper::class);
            $definitionForm->addTag(self::TEMPLATING_HELPER_TAG, ['alias' => 'form']);
            $container->setDefinition('nyrodev.templating.helper.form', $definitionForm);
        }
    }
}
