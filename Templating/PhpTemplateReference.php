<?php

namespace NyroDev\PhpTemplateBundle\Templating;

use Symfony\Component\Templating\TemplateReference;

class PhpTemplateReference extends TemplateReference
{
    public function __construct(?string $name = null, ?string $engine = null, ?string $bundle = null)
    {
        parent::__construct($name, $engine);
        $this->parameters['bundle'] = $bundle;
    }
}
