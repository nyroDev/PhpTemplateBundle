# PhpTemplateBundle
Symfony Bundle in order to keep using PHP template

*This was not tested properly, use with caution*

# Instalation

```
composer require nyrodev/php-template-bundle dev-master
```

# Usage

As you did before on Symfony 4, simply use your php template when rendering something.
Usage examples :
```
$this->render('Admin/template.html.php', /* ... */);
$this->render('@MyBundle/Admin/template.html.php', /* ... */);
```

All helpers extings on Symfony 4 was also ported, like `assets`, `form`, `session`, etc...
It means you can still use them on your PHP templates with `$view['helper']->call()`

Moreover, the tag `templating.helper` is still working.  
You can still define your own PHP template helper by simply adding this tag, just like before.

# Notes

PHP Form templates does *NOT* work.  
It requires more works and it shouldn't be too much work to rewrite only these templates into Twig.

Many source code comes from the [last version 4 of framework bundle](https://github.com/symfony/framework-bundle/tree/4.4).
For now, the only changes made on these files have been removing deprecation notices and changing namespaces.

