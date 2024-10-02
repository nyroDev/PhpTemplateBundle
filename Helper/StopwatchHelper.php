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

use BadMethodCallException;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Templating\Helper\Helper;

/**
 * StopwatchHelper provides methods time your PHP templates.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class StopwatchHelper extends Helper
{
    public function __construct(
        private readonly ?Stopwatch $stopwatch = null,
    ) {
    }

    public function getName(): string
    {
        return 'stopwatch';
    }

    public function __call($method, $arguments = [])
    {
        if (null === $this->stopwatch) {
            return null;
        }

        if (method_exists($this->stopwatch, $method)) {
            return $this->stopwatch->{$method}(...$arguments);
        }

        throw new BadMethodCallException(sprintf('Method "%s" of Stopwatch does not exist.', $method));
    }
}
