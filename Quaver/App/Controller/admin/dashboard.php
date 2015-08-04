<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller\admin;

use Quaver\Core\Controller;
use Quaver\Core\Log;
use Quaver\Core\LangStrings;
use Quaver\Core\Lang;
use Quaver\App\Model\User;

/**
 * Dashboard controller.
 */
class dashboard extends Controller
{
    /**
     * Show home.
     *
     * @return mixed
     */
    public function homeAction()
    {
        global $_user;

        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header('Location: /login');
            exit;
        }

        // Set up menu action
        $this->addTwigVars('section', '');

        $this->setView('admin/main');
        $this->render();
    }

    /**
     * Manage languages.
     *
     * @return mixed
     */
    public function languagesAction()
    {
        global $_user;

        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header('Location: /login');
            exit;
        }

        // Control var
        $added = false;

        // Set up menu action
        $this->addTwigVars('section', 'languages');

        // Add or edit language strings
        if (isset($_POST['edit']) || isset($_POST['add'])) {
            $added = false;
            $isNew = false;
            $item = new LangStrings();

            foreach ($_POST['language'] as $k => $v) {
                $new_lang = new LangStrings();

                if ($_POST['idL'][$k]) {
                    $new_lang->id = $_POST['idL'][$k];
                } else {
                    $isNew = true;
                    $new_lang->id = null;
                }

                $new_lang->language = $_POST['language'][$k];
                $new_lang->label = $_POST['label'];
                $new_lang->text = $_POST['text'][$k];

                $_item['_languages'][] = $new_lang;
            }

            $item->setItem($_item);

            if ($item->saveAll()) {
                Log::notify($GLOBALS['_user'], $isNew ? Log::ACTION_CREATE : Log::ACTION_UPDATE, $item);
                header('Location: /admin/languages');
                exit;
            } else {
                $added = false;
            }
        }

        // Selector
        switch ($this->router->getUrlPart(0)) {

            case('add') :
                $this->addTwigVars('typePOST', 'add');
                if ($added) {
                    header('Location: /admin/languages');
                    exit;
                } else {
                    $this->setView('admin/langString-Add');
                }
                break;
            case('edit'):
                $this->addTwigVars('typePOST', 'edit');
                $lang = new LangStrings();
                $item = $lang->getFromLabel($this->router->url['uri'][1]);
                $this->addTwigVars('item', $item);

                $this->setView('admin/langString-Add');
                break;
            case('del'):
                $lang = new LangStrings();
                $items = $lang->getFromLabel($this->router->url['uri'][1]);

                foreach ($items as $item) {
                    $item->delete();
                    Log::notify($GLOBALS['_user'], Log::ACTION_DELETE, $item);
                }

                header('Location: /admin/languages');
                exit;

                break;
            default:
                $lang = new LangStrings();
                $items = $lang->getList();
                $this->addTwigVars('items', $items);

                $this->setView('admin/langString-List');
                break;
        }
        $this->render();
    }

    /**
     * Manage users.
     *
     * @return mixed
     */
    public function usersAction()
    {
        global $_user;
        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header('Location: /login');
            exit;
        }

        // Control var
        $added = false;

        // Set up menu action
        $this->addTwigVars('section', 'users');

        // Add or edit users strings
        if (isset($_POST['edit']) || isset($_POST['add'])) {
            $added = false;
            $isNew = false;

            $user = new User();

            if (isset($_POST['id'])) {
                $user->getFromId($_POST['id']);
            } else {
                $isNew = true;
            }

            $user->level = $_POST['level'];
            $user->active = $_POST['active'];

            if (isset($_POST['password'])) {
                $user->password = $user->hashPassword($_POST['password']);
            } else {
                $user->password = 0;
            }

            $user->email = $_POST['email'];

            if (isset($_POST['add'])) {
                $user->dateRegister = date('Y-m-d H:i:s', time());
                $user->dateLastLogin = date('Y-m-d H:i:s', time());
            }

            if ($user->save()) {
                Log::notify($GLOBALS['_user'], $isNew ? Log::ACTION_CREATE : Log::ACTION_UPDATE, $user);
                header('Location: /admin/users');
                exit;
            } else {
                $added = false;
            }
        }

        // Selector
        switch ($this->router->getUrlPart(0)) {

            case('add') :
                $this->addTwigVars('typePOST', 'add');
                if ($added) {
                    header('Location: /admin/users');
                    exit;
                } else {
                    $this->setView('admin/user-Add');
                }
                break;
            case('edit'):
                $this->addTwigVars('typePOST', 'edit');
                $user = new User();
                $item = $user->getFromId($this->router->url['uri'][1]);
                $this->addTwigVars('item', $item);
                $this->setView('admin/user-Add');
                break;
            case('del'):
                $user = new User();
                $item = $user->getFromId($this->router->url['uri'][1]);
                if ($item->delete()) {
                    Log::notify($GLOBALS['_user'], Log::ACTION_DELETE, $item);
                    header('Location: /admin/users');
                    exit;
                }
                break;
            default:
                $user = new User();
                $items = $user->getList();
                $this->addTwigVars('items', $items);
                $this->setView('admin/user-List');
                break;
        }
        $this->render();
    }

    /**
     * Manage lang table.
     *
     * @return mixed
     */
    public function langAction()
    {
        global $_user;

        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header('Location: /login');
            exit;
        }

        // Control var
        $added = false;

        // Set up menu action
        $this->addTwigVars('section', 'lang');

        // Add or edit users strings
        if (isset($_POST['edit']) || isset($_POST['add'])) {
            $item = new Lang();
            $isNew = false;

            if (empty($_POST['id'])) {
                $isNew = true;
            } else {
                $item->getFromId($_POST['id']);
            }

            $item->name = $_POST['name'];
            $item->large = $_POST['large'];
            $item->slug = $_POST['slug'];
            $item->locale = $_POST['locale'];
            $item->active = $_POST['active'];
            $item->priority = $_POST['priority'];

            if ($item->save()) {
                Log::notify($GLOBALS['_user'], $isNew ? Log::ACTION_CREATE : Log::ACTION_UPDATE, $item);
                header('Location: /admin/lang');
                exit;
            } else {
                $added = false;
            }
        }

        // Selector
        switch ($this->router->getUrlPart(0)) {

            case('add') :
                $this->addTwigVars('typePOST', 'add');
                if ($added) {
                    header('Location: /admin/lang');
                    exit;
                } else {
                    $this->setView('admin/language-Add');
                }
                $this->setView('admin/language-Add');
                break;
            case('edit'):
                $this->addTwigVars('typePOST', 'edit');
                $lang = new Lang();
                $item = $lang->getFromId($this->router->getUrlPart(1));
                $this->addTwigVars('item', $item);
                $this->setView('admin/language-Add');
                break;
            case('del'):
                $item = new Lang();
                $item->getFromId($this->router->getUrlPart(1));
                if ($item->id) {
                    $item->delete();
                }
                Log::notify($GLOBALS['_user'], Log::ACTION_DELETE, $item);
                header('Location: /admin/lang');
                exit;
                break;
            default:
                $lang = new Lang();
                $items = $lang->getList(false);
                $this->addTwigVars('items', $items);
                $this->setView('admin/language-List');
                break;
        }

        $this->render();
    }

    /**
     * See log table.
     *
     * @return mixed
     */
    public function logAction()
    {
        $this->addTwigVars('section', 'log');

        switch (count($this->router->url['uri'])) {
            case 0:
                $log = new Log();
                $items = $log->getList();

                $this->addTwigVars('items', $items);
                $this->setView('admin/log-List');
                break;

            default:
                $this->router->dispatch('e404');
                exit;
        }

        $this->render();
    }

    /**
     * Plugins manager.
     *
     * @return mixed
     */
    public function pluginsAction()
    {
        global $_user;

        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header('Location: /login');
            exit;
        }

        // Set up menu action
        $this->addTwigVars('section', 'plugins');

        $this->setView('admin/plugins-List');
        $this->render();
    }

    /**
     * Routing manager.
     *
     * @return mixed
     */
    public function routingAction()
    {
        global $_user;

        // Check privileges
        if (!$_user->logged || !$_user->isAdmin()) {
            header('Location: /login');
            exit;
        }

        // Set up menu action
        $this->addTwigVars('section', 'routing');

        $this->setView('admin/routing-List');
        $this->render();
    }
}
