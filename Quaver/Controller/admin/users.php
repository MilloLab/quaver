<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Controller;
use Quaver\Model\User;

// Check privileges
if (!$_user->logged || !$_user->isAdmin()) {
    header("Location: /");
    exit;
} 

// Set up menu action
$this->addTwigVars('section', 'users');

// Add or edit language strings
if (@isset($_POST['edit']) || @isset($_POST['add'])) {
	$added = false;

    $item = new User;

    foreach ($_POST['language'] as $k => $v) {
        
        $new_lang = new LangStrings;
        
        if ($_POST['idL'][$k]){
            $_l['id'] = $_POST['idL'][$k];
        } else {
            $_l['id'] = null;
        }
        
        $_l['language'] = $_POST['language'][$k];
        $_l['label'] = $_POST['label'];
        $_l['text'] = $_POST['text'][$k];
        
        $new_lang->setItem($_l);

        $_item['_languages'][] = $new_lang;  
    
    }
    
    $item->setItem($_item);      

    if ($item->saveAll()) {
        header("Location: /admin/languages");
        exit;
    } else {
        $added = false;
    }
}

// Selector
switch ($this->url_var[1]) {

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




