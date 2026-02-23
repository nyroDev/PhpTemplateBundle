<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->private();

    $services->set('nyrodev.templating.engine.php.helpers_locator')
        ->args([[]])
        ->tag('container.service_locator');

    $services->set(\Symfony\Component\Templating\Helper\SlotsHelper::class, \Symfony\Component\Templating\Helper\SlotsHelper::class)
        ->tag('templating.helper', ['alias' => 'slots']);

    $services->set(\NyroDev\PhpTemplateBundle\Helper\RequestHelper::class, \NyroDev\PhpTemplateBundle\Helper\RequestHelper::class)
        ->args([service('request_stack')])
        ->tag('templating.helper', ['alias' => 'request']);

    $services->set(\NyroDev\PhpTemplateBundle\Helper\SessionHelper::class, \NyroDev\PhpTemplateBundle\Helper\SessionHelper::class)
        ->args([service('request_stack')])
        ->tag('templating.helper', ['alias' => 'session']);

    $services->set(\NyroDev\PhpTemplateBundle\Helper\SecurityHelper::class, \NyroDev\PhpTemplateBundle\Helper\SecurityHelper::class)
        ->args([
            service('security.helper')->nullOnInvalid(),
            service('security.impersonate_url_generator')->nullOnInvalid(),
        ])
        ->tag('templating.helper', ['alias' => 'security']);

    $services->set(\NyroDev\PhpTemplateBundle\Helper\RouterHelper::class, \NyroDev\PhpTemplateBundle\Helper\RouterHelper::class)
        ->args([service('router')])
        ->tag('templating.helper', ['alias' => 'router']);

    $services->set(\NyroDev\PhpTemplateBundle\Helper\ActionsHelper::class, \NyroDev\PhpTemplateBundle\Helper\ActionsHelper::class)
        ->args([service('fragment.handler')])
        ->tag('templating.helper', ['alias' => 'actions']);

    $services->set(\NyroDev\PhpTemplateBundle\Helper\CodeHelper::class, \NyroDev\PhpTemplateBundle\Helper\CodeHelper::class)
        ->args([
            service('debug.file_link_formatter'),
            '%kernel.project_dir%',
            '%kernel.charset%',
        ])
        ->tag('templating.helper', ['alias' => 'code']);

    $services->set(\NyroDev\PhpTemplateBundle\Helper\TranslatorHelper::class, \NyroDev\PhpTemplateBundle\Helper\TranslatorHelper::class)
        ->args([service('translator')->nullOnInvalid()])
        ->tag('templating.helper', ['alias' => 'translator']);

    $services->set(\NyroDev\PhpTemplateBundle\Helper\StopwatchHelper::class, \NyroDev\PhpTemplateBundle\Helper\StopwatchHelper::class)
        ->args([service('debug.stopwatch')->ignoreOnInvalid()])
        ->tag('templating.helper', ['alias' => 'stopwatch']);
};
