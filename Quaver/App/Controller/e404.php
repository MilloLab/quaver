<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Controller;

/**
 * Error404 controller
 * @package App
 */
class e404 extends Controller
{
    public function indexAction()
    {   
        global $_lang;
        
        $url = $this->router->getCurrentRoute();

        header("HTTP/1.0 404 Not Found");
        trigger_error("[404] $url", E_USER_WARNING);

        if (!defined('AJAX_METHOD')) {
            $this->addTwigVars('siteTitle', $_lang->l('title-404') . ' - ' . BRAND_NAME);
            $this->render();
        }
    }
}
