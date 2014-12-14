<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Controller;

$url = $this->getCurrentRoute();

header("HTTP/1.0 404 Not Found");
trigger_error("[404] $url", E_USER_WARNING);

$this->addTwigVars('siteTitle', $_lang->l('title-404') . ' - ' . BRAND_NAME);
$template = $this->twig->loadTemplate("404.twig");
echo $template->render($this->twigVars);
