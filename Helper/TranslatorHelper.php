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

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TranslatorHelper extends Helper
{
    use TranslatorTrait {
        getLocale as private;
        setLocale as private;
        trans as private doTrans;
    }

    public function __construct(
        private readonly ?TranslatorInterface $translator = null,
    ) {
    }

    /**
     * @see TranslatorInterface::trans()
     */
    public function trans(string $id, array $parameters = [], string $domain = 'messages', ?string $locale = null): string
    {
        if (null === $this->translator) {
            return $this->doTrans($id, $parameters, $domain, $locale);
        }

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @see TranslatorInterface::transChoice()
     * @deprecated since Symfony 4.2, use the trans() method instead with a %count% parameter
     */
    public function transChoice(string $id, $number, array $parameters = [], string $domain = 'messages', ?string $locale = null): stirng
    {
        @trigger_error(sprintf('The "%s()" method is deprecated since Symfony 4.2, use the trans() one instead with a "%%count%%" parameter.', __METHOD__), E_USER_DEPRECATED);

        if (null === $this->translator) {
            return $this->doTrans($id, ['%count%' => $number] + $parameters, $domain, $locale);
        }
        if ($this->translator instanceof TranslatorInterface) {
            return $this->translator->trans($id, ['%count%' => $number] + $parameters, $domain, $locale);
        }

        return $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
    }

    public function getName(): string
    {
        return 'translator';
    }
}
