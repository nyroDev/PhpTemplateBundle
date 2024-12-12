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

use Exception;
use NyroDev\PhpTemplateBundle\Helper\AssetsHelper;
use NyroDev\PhpTemplateBundle\Helper\FormHelper;
use NyroDev\PhpTemplateBundle\Helper\TagRendererHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use function count;

class TemplatingPass implements CompilerPassInterface
{
    public const TEMPLATING_HELPER_TAG = 'templating.helper';

    public function process(ContainerBuilder $container)
    {
        $refs = [];
        $helpers = [];

        if ($container->hasDefinition('nyrodev.templating.php.templating')) {
            foreach ($container->findTaggedServiceIds(self::TEMPLATING_HELPER_TAG, true) as $id => $attributes) {
                if (isset($attributes[0]['alias'])) {
                    $helpers[$attributes[0]['alias']] = $id;
                    $refs[$id] = new Reference($id);
                } elseif (is_callable($id.'::getAlias')) {
                    $helpers[call_user_func($id.'::getAlias')] = $id;
                    $refs[$id] = new Reference($id);
                } else {
                    throw new Exception('Tag '.self::TEMPLATING_HELPER_TAG.' found, not alias provided');
                }
            }
        }

        if ($container->hasDefinition('assets.packages')) {
            // Add helper
            $id = AssetsHelper::class;
            $definitionAssets = new Definition($id);
            $definitionAssets->setArguments([
                $container->getDefinition('assets.packages'),
            ]);
            $definitionAssets->addTag(self::TEMPLATING_HELPER_TAG, ['alias' => 'assets']);
            $container->setDefinition($id, $definitionAssets);

            $helpers['assets'] = $id;
            $refs[$id] = new Reference($id);

            // Add tag renderer
            if ($container->hasDefinition('webpack_encore.entrypoint_lookup_collection')) {
                $idRenderer = 'nyrodev_tagRenderer';
                $classRenderer = TagRendererHelper::class;

                $definitionRenderer = new Definition($classRenderer);
                $definitionRenderer->setArguments([
                    $container->getDefinition('assets.packages'),
                    $container->getDefinition('webpack_encore.entrypoint_lookup_collection'),
                ]);

                if ($container->hasParameter('nyrodev_templating')) {
                    $nyrodevTemplating = $container->getParameter('nyrodev_templating');
                    if (is_array($nyrodevTemplating) && isset($nyrodevTemplating['injectedAssets'])) {
                        $definitionRenderer->addMethodCall('setInjectedAssets', [$nyrodevTemplating['injectedAssets']]);
                    }
                }

                $definitionRenderer->addTag(self::TEMPLATING_HELPER_TAG, ['alias' => 'nyrodev_tagRenderer']);
                $container->setDefinition($idRenderer, $definitionRenderer);

                $container->setAlias($classRenderer, $idRenderer);

                $helpers['nyrodev_tagRenderer'] = $idRenderer;
                $refs[$idRenderer] = new Reference($idRenderer);
            }
        }

        if ($container->hasDefinition('twig.form.renderer')) {
            $id = FormHelper::class;
            $definitionForm = new Definition($id);
            $definitionForm->setArguments([
                $container->getDefinition('twig.form.renderer'),
            ]);
            $definitionForm->addTag(self::TEMPLATING_HELPER_TAG, ['alias' => 'form']);
            $container->setDefinition($id, $definitionForm);

            $helpers['form'] = $id;
            $refs[$id] = new Reference($id);
        }

        if (count($helpers) > 0) {
            $definition = $container->getDefinition('nyrodev.templating.php.templating');
            $definition->addMethodCall('setHelpers', [$helpers]);

            if ($container->hasDefinition('nyrodev.templating.engine.php.helpers_locator')) {
                $container->getDefinition('nyrodev.templating.engine.php.helpers_locator')->replaceArgument(0, $refs);
            }
        }
    }
}
