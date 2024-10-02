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

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Http\Impersonate\ImpersonateUrlGenerator;
use Symfony\Component\Templating\Helper\Helper;

/**
 * SessionHelper provides read-only access to the session attributes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SecurityHelper extends Helper
{
    public function __construct(
        private readonly ?AuthorizationCheckerInterface $securityChecker = null,
        private readonly ?ImpersonateUrlGenerator $impersonateUrlGenerator = null,
    ) {
    }

    public function isGranted(mixed $attribute, mixed $subject = null): bool
    {
        if (null === $this->securityChecker) {
            return false;
        }

        try {
            return $this->securityChecker->isGranted($attribute, $subject);
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    public function getImpersonateExitUrl(?string $exitTo = null): string
    {
        if (null === $this->impersonateUrlGenerator) {
            return '';
        }

        return $this->impersonateUrlGenerator->generateExitUrl($exitTo);
    }

    public function getImpersonateExitPath(?string $exitTo = null): string
    {
        if (null === $this->impersonateUrlGenerator) {
            return '';
        }

        return $this->impersonateUrlGenerator->generateExitPath($exitTo);
    }

    public function getName(): string
    {
        return 'security';
    }
}
