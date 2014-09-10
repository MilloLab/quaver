What is Quaver?
===============
It is a framework designed to develop from a basic website to a platform. Lightweight and simple to use.

Quaver is developed by [Alberto González](https://github.com/albertogonzcat) & [MilloLab](http://millolab.com), and distributed under MIT license.

[![Latest Stable Version](https://poser.pugx.org/millolab/quaver/v/stable.svg)](https://packagist.org/packages/millolab/quaver) [![Total Downloads](https://poser.pugx.org/millolab/quaver/downloads.svg)](https://packagist.org/packages/millolab/quaver) [![Latest Unstable Version](https://poser.pugx.org/millolab/quaver/v/unstable.svg)](https://packagist.org/packages/millolab/quaver) [![License](https://poser.pugx.org/millolab/quaver/license.svg)](https://packagist.org/packages/millolab/quaver)

Installation
------------
* Import `quaver.sql` and check `app/config.php` and `app/routes.yml`.
* Optional: 
	* Customize Mail model with `PHPMailer/Mandrill` functions.
	* If you want use *users* in your project check `model/user_default`.

Requeriments
------------
* PHP >= 5.3
* MYSQL PDO

History
-------
* Version 0.4 (September 2014)
	* Redesign with namespaces and new structure
* Version 0.3 (September 2014)
	* Set new core and new internal flow.
	* New functions and extended models.
* Version 0.2 (Summer 2014)
	* Set new functions.
* Version 0.1 (Summer 2014)
	* First version.
	* Core level 1.
	* Multilanguage supported.

See [changelog](https://github.com/MilloLab/quaver/blob/master/changelog.md) for details.


External Lib
------------
* [Twig](http://twig.sensiolabs.org/) by SensioLabs.
* [YAML Component](http://symfony.com/doc/current/components/yaml/introduction.html) of Symfony.
* [PHPMailer](https://github.com/PHPMailer/PHPMailer).
* [Mandrill PHP API Client](https://mandrillapp.com/api/docs/).

Thanks to
---------
* [Felipe (fmartingr)](https://github.com/fmartingr).
* AwesomezGuy.