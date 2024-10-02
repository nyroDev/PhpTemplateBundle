<?php

namespace NyroDev\PhpTemplateBundle\Helper;

use Symfony\Component\Templating\Helper\HelperInterface as SrcHelperInterface;

interface HelperInterface extends SrcHelperInterface
{
    public static function getAlias(): string;
}
