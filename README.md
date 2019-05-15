EMSMakerBundle
=============

Allows the creation of a demo site, and the creation of default configurations for website creators

Coding standards (TODO)
----------------
PHP Code Sniffer is available via composer, the standard used is defined in phpcs.xml.diff:
````bash
composer phpcs
````

If your code is not compliant, you could try fixing it automatically:
````bash
composer phpcbf
````

PHPStan is run at level 7, you can check for errors locally using:
`````bash
composer phpstan
`````

Documentation
-------------

[Configuration](../master/Resources/doc/configuration.md)