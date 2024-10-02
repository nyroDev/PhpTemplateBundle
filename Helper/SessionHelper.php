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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * SessionHelper provides read-only access to the session attributes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SessionHelper extends Helper
{
    private ?SessionInterface $session = null;

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Returns an attribute.
     *
     * @param string $name    The attribute name
     * @param mixed  $default The default value
     */
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->getSession()->get($name, $default);
    }

    public function getFlash(string $name, array $default = []): array
    {
        return $this->getSession()->getFlashBag()->get($name, $default);
    }

    public function getFlashes(): array
    {
        return $this->getSession()->getFlashBag()->all();
    }

    public function hasFlash(string $name)
    {
        return $this->getSession()->getFlashBag()->has($name);
    }

    private function getSession(): SessionInterface
    {
        if (null === $this->session) {
            if (!$this->requestStack->getMainRequest()) {
                throw new LogicException('A Request must be available.');
            }

            $this->session = $this->requestStack->getMainRequest()->getSession();
        }

        return $this->session;
    }

    public function getName(): string
    {
        return 'session';
    }
}
