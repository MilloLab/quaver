<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller\admin;

use Quaver\Core\Controller;
use Quaver\Core\LangStrings;
use Quaver\App\Model\User;

/**
 * Dashboard controller
 * @package App
 */
class dashboard extends Controller
{

    /**
     * Show home
     * @return type
     */
    public function homeAction()
    {   
        global $_user;

        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header("Location: /login");
            exit;
        } 

        // Set up menu action
        $this->addTwigVars('section', '');
        $this->setView('admin/main');
        $this->render();
    }

    /**
     * Manage languages
     * @return type
     */
    public function languagesAction()
    {
        global $_user;

        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header("Location: /login");
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
        switch ($this->router->getUrlPart(0)) {

            case('add'):
                $this->addTwigVars('typePOST', 'add');
                if ($added){
                    header("Location: /admin/languages");
                    exit;
                } else {
                    $this->setView('admin/lang-Add');
                }
                break;
            case('edit'):
                $this->addTwigVars('typePOST', 'edit');
                $lang = new LangStrings;
                $item = $lang->getFromLabel($this->router->url['uri'][1]);
                $this->addTwigVars('item', $item);

                $this->setView('admin/lang-Add');                
                break;
            case('del'):
                $lang = new LangStrings;
                $items = $lang->getFromLabel($this->router->url['uri'][1]);

                foreach ($items as $item) {
                    $item->delete();
                }

                header("Location: /admin/languages");
                exit;
                
                break;
            default:
                $lang = new LangStrings;
                $items = $lang->getList();
                $this->addTwigVars('items', $items);

                $this->setView('admin/lang-List');
                break;
        }
        $this->render();
    }

    /**
     * Manage users
     * @return type
     */
    public function usersAction()
    {
        global $_user;
        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header("Location: /login");
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

            if (isset($_POST['id'])) {
                $user->getFromId($_POST['id']);
            }

            $user->level = $_POST['level'];
            $user->active = $_POST['active'];

            if (isset($_POST['password'])) {
                $user->password = $user->hashPassword($_POST['password']);
            } else {
                $user->password = 0;
            }
            
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
        switch ($this->router->getUrlPart(0)) {

            case('add'):
                $this->addTwigVars('typePOST', 'add');
                if ($added){
                    header("Location: /admin/users");
                    exit;
                } else {
                    $this->setView('admin/user-Add');
                }
                break;
            case('edit'):
                $this->addTwigVars('typePOST', 'edit');
                $user = new User;
                $item = $user->getFromId($this->router->url['uri'][1]);
                $this->addTwigVars('item', $item);
                $this->setView('admin/user-Add');
                break;
            case('del'):
                $user = new User;
                $item = $user->getFromId($this->router->url['uri'][1]);
                if ($item->delete()){
                    header("Location: /admin/users");
                    exit;
                }
                break;
            default:
                $user = new User;
                $items = $user->getList();
                $this->addTwigVars('items', $items);
                $this->setView('admin/user-List');
                break;
        }
        $this->render();
    }
}
