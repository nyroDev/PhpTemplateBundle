<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NyroDev\PhpTemplateBundle\Helper;

use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\Templating\Helper\Helper;

/**
 * ActionsHelper manages action inclusions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ActionsHelper extends Helper
{
    public function __construct(
        private readonly FragmentHandler $handler,
    ) {
    }

    /**
     * Returns the fragment content for a given URI.
     *
     * @see FragmentHandler::render()
     */
    public function render(string|ControllerReference $uri, array $options = []): string
    {
        $strategy = isset($options['strategy']) ? $options['strategy'] : 'inline';
        unset($options['strategy']);

        return $this->handler->render($uri, $strategy, $options);
    }

    public function controller(string $controller, array $attributes = [], array $query = []): ControllerReference
    {
        return new ControllerReference($controller, $attributes, $query);
    }

    public function getName(): string
    {
        return 'actions';
    }
}
