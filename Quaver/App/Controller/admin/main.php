<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

// Check privileges
if (!$_user->logged || !$_user->isAdmin()) {
    header("Location: /");
    exit;
} 

// Set up menu action
$this->addTwigVars('section', '');

// Load template with data
$template = $this->twig->loadTemplate('admin/main.twig');
echo $template->render($this->twigVars);
