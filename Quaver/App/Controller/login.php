<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\App\Model\User;

if ($_user->logged){
	header("Location: /");
    exit;
}

// REF
$goTo = '/';
if (isset($_GET['ref'])) {
    $goTo = strip_tags(addslashes($_GET['ref']));
} else if (isset($_SERVER['HTTP_REFERER'])) {
    $goTo = strip_tags(addslashes($_SERVER['HTTP_REFERER']));
}

if (isset($_POST['email']) && isset($_POST['password'])
    && !empty($_POST['email']) && !empty($_POST['password'])) {

    $user = new User;
    if ($user->getFromEmailPassword($_POST['email'], $_POST['password']) > 0) {
        if ($user->isActive()) {
            // Logged in
            $user->setCookie();
        } else {
            // User not active
            $goTo = '/login/?user-disabled';
        }
    } else {
        // Error logging in
        $goTo = '/login/?login-error';
    }
    
	header("Location: $goTo");
	exit;
}

$template = $this->twig->loadTemplate("login.twig");
echo $template->render($this->twigVars);
