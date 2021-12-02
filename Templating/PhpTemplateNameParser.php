<?php

namespace NyroDev\PhpTemplateBundle\Templating;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class PhpTemplateNameParser implements TemplateNameParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse(string|TemplateReferenceInterface $name): TemplateReferenceInterface
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        $engine = null;
        if (false !== $pos = strrpos($name, '.')) {
            $engine = substr($name, $pos + 1);
        }

        $bundle = null;

        $tmp = explode('/', $name);
        if (
            count($tmp) > 1
            && '@' === substr($tmp[0], 0, 1)
        ) {
            $bundle = substr($tmp[0], 1);
            unset($tmp[0]);
            $name = implode('/', $tmp);
        }

        return new PhpTemplateReference($name, $engine, $bundle);
    }
}
