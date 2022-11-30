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

use Symfony\Component\Security\Acl\Voter\FieldVote;
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
    private ?AuthorizationCheckerInterface $securityChecker;
    private ?ImpersonateUrlGenerator $impersonateUrlGenerator;

    public function __construct(AuthorizationCheckerInterface $securityChecker = null, ImpersonateUrlGenerator $impersonateUrlGenerator = null)
    {
        $this->securityChecker = $securityChecker;
        $this->impersonateUrlGenerator = $impersonateUrlGenerator;
    }

    public function isGranted(mixed $role, mixed $object = null, string $field = null): bool
    {
        if (null === $this->securityChecker) {
            return false;
        }

        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        try {
            return $this->securityChecker->isGranted($role, $object);
        } catch (AuthenticationCredentialsNotFoundException) {
            return false;
        }
    }

    public function getImpersonateExitUrl(string $exitTo = null): string
    {
        if (null === $this->impersonateUrlGenerator) {
            return '';
        }

        return $this->impersonateUrlGenerator->generateExitUrl($exitTo);
    }

    public function getImpersonateExitPath(string $exitTo = null): string
    {
        if (null === $this->impersonateUrlGenerator) {
            return '';
        }

        return $this->impersonateUrlGenerator->generateExitPath($exitTo);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'security';
    }
}
