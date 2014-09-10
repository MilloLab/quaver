<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Controller;

$template = $this->twig->loadTemplate('home.twig');
echo $template->render($this->twigVars);

?>
