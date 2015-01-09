<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

$this->addTwigVars('siteTitle', "Welcome to Quaver" . ' - ' . BRAND_NAME);
$template = $this->twig->loadTemplate('home.twig');
echo $template->render($this->twigVars);
