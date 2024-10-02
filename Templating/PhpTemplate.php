<?php

namespace NyroDev\PhpTemplateBundle\Templating;

use Twig\Environment;

class PhpTemplate extends Environment
{
    protected ?PhpEngine $templating = null;

    public function setTemplating(PhpEngine $templating): void
    {
        $this->templating = $templating;
    }

    public function render($name, array $context = []): string
    {
        if ($this->templating->supports($name)) {
            return $this->templating->render($name, $context);
        }

        return parent::render($name, $context);
    }
}
