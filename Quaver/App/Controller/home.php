<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Controller;

class home extends Controller
{
    public function homeAction()
    {
        $this->addTwigVars('siteTitle', "Welcome to Quaver" . ' - ' . BRAND_NAME);
        $this->render();
    }
}


