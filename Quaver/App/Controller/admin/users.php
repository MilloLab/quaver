<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\App\Model\User;

// Check privileges
if (!$_user->logged || !$_user->isAdmin()) {
    header("Location: /");
    exit;
} 

// Control var
$added = false;

// Set up menu action
$this->addTwigVars('section', 'users');

// Add or edit users strings
if (isset($_POST['edit']) || isset($_POST['add'])) {
	$added = false;

    $user = new User;

    if ($_POST['id']){
        $user->id = $_POST['id'];
    }

    $user->level = $_POST['level'];
    $user->active = $_POST['active'];
    $user->password = $user->hashPassword($_POST['password']);
    $user->email = $_POST['email'];

    if (isset($_POST['add'])){
        $user->dateRegister = date('Y-m-d H:i:s', time());
        $user->dateLastLogin = date('Y-m-d H:i:s', time());    
    }

    if ($user->save()) {
        header("Location: /admin/users");
        exit;
    } else {
        $added = false;
    }
    
}

// Selector
switch ($this->getCurrentURL()) {

    case('add'):
    	$this->addTwigVars('typePOST', 'add');
    	if ($added){
	    	header("Location: /admin/users");
	    	exit;
    	} else {
            // Load template with data
	    	$template = $this->twig->loadTemplate('admin/user-Add.twig');
    	}
    	echo $template->render($this->twigVars);
    	break;
    case('edit'):
   	 	$this->addTwigVars('typePOST', 'edit');
        $user = new User;
    	$item = $user->getFromId($this->url_var[2]);
    	$this->addTwigVars('item', $item);

        // Load template with data
	    $template = $this->twig->loadTemplate('admin/user-Add.twig');
    	echo $template->render($this->twigVars);
    	break;
    case('del'):
        $user = new User;
	    $item = $user->getFromId($this->url_var[2]);
        
        if ($item->delete()){
            header("Location: /admin/users");
            exit;
        }
        
        break;
    default:
        $user = new User;
    	$item = $user->getList();
		$this->addTwigVars('items', $item);

        // Load template with data
		$template = $this->twig->loadTemplate('admin/user-List.twig');
		echo $template->render($this->twigVars);
        break;
}
