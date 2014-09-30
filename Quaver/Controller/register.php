<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Controller;
use Quaver\Model\User;

if ($_user->logged){
    header("Location: /");
    exit;
}

// REF
$goTo = '/';
if (!empty($_GET['ref'])) {
    $goTo = strip_tags(addslashes($_GET['ref']));
} else if (!empty($_SERVER['HTTP_REFERER'])) {
    $goTo = strip_tags(addslashes($_SERVER['HTTP_REFERER']));
}

if (isset($_POST['email']) && isset($_POST['password'])
    && !empty($_POST['email']) && !empty($_POST['password'])) {

    $user = new User;
    $_error = false;
    

    // Checking if mail already registered
    if ($user->isEmailRegistered($_POST['email'])) {
        $_error = true;
    }

    // Check errors to continue
    if (!$_error) {

        $item['active'] = 1;
        $item['password'] = $user->hashPassword($_POST['password']);
        $item['email'] = $_POST['email'];

        if ($_POST['admin'] == true){
            $item['level'] = "admin";
        } else {
            $item['level'] = "user";    
        }
        
        $item['dateRegister'] = date('Y-m-d H:i:s', time());
        $item['dateLastLogin'] = date('Y-m-d H:i:s', time());
        
        $user->setItem($item);

        if ($user->save()) {
        
            $user->cookie();
            $user->setCookie();
            
            header("Location: $goTo");
            exit;
        }
    }
}


$template = $this->twig->loadTemplate("register.twig");
echo $template->render($this->twigVars);

?>
