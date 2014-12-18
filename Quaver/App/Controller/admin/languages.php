<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\LangStrings;

// Check privileges
if (!$_user->logged || !$_user->isAdmin()) {
    header("Location: /");
    exit;
} 

// Control var
$added = false;

// Set up menu action
$this->addTwigVars('section', 'languages');

// Add or edit language strings
if (isset($_POST['edit']) || isset($_POST['add'])) {
	$added = false;

    $item = new LangStrings;

    foreach ($_POST['language'] as $k => $v) {
        
        $new_lang = new LangStrings;
        
        if ($_POST['idL'][$k]){
            $new_lang->id = $_POST['idL'][$k];
        } else {
            $new_lang->id = null;
        }
        
        $new_lang->language = $_POST['language'][$k];
        $new_lang->label = $_POST['label'];
        $new_lang->text = $_POST['text'][$k];

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
switch ($this->getCurrentURL()) {

    case('add'):
    	$this->addTwigVars('typePOST', 'add');
    	if ($added){
	    	header("Location: /admin/languages");
	    	exit;
    	} else {
            // Load template with data
	    	$template = $this->twig->loadTemplate('admin/lang-Add.twig');
    	}
    	echo $template->render($this->twigVars);
    	break;
    case('edit'):
   	 	$this->addTwigVars('typePOST', 'edit');
        $lang = new LangStrings;
    	$item = $lang->getFromLabel($this->url_var[2]);
    	$this->addTwigVars('item', $item);

        // Load template with data
	    $template = $this->twig->loadTemplate('admin/lang-Add.twig');
    	echo $template->render($this->twigVars);
    	break;
    case('del'):
        $lang = new LangStrings;
	    $items = $lang->getFromLabel($this->url_var[2]);

        foreach ($items as $item) {
            $item->delete();
        }

        header("Location: /admin/languages");
        exit;
        
		break;
    default:
        $lang = new LangStrings;
    	$items = $lang->getLanguageList();
		$this->addTwigVars('items', $items);

        // Load template with data
		$template = $this->twig->loadTemplate('admin/lang-List.twig');
		echo $template->render($this->twigVars);
        break;
}
