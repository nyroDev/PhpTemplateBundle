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

use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\Helper\Helper;

/**
 * RequestHelper provides access to the current request parameters.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RequestHelper extends Helper
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Returns a parameter from the current request object.
     *
     * @param string $key     The name of the parameter
     * @param string $default A default value
     *
     * @see Request::get()
     */
    public function getParameter(string $key, ?string $default = null): mixed
    {
        return $this->getRequest()->get($key, $default);
    }

    /**
     * Returns the locale.
     */
    public function getLocale(): string
    {
        return $this->getRequest()->getLocale();
    }

    public function getRequest(): Request
    {
        if (!$this->requestStack->getCurrentRequest()) {
            throw new LogicException('A Request must be available.');
        }

        return $this->requestStack->getCurrentRequest();
    }

    public function getName(): string
    {
        return 'request';
    }
}
