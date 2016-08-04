<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Controller;
use Quaver\Core\Config;

class home extends Controller
{
    public function indexAction() {
        $this->addTwigVars('helloWorld', 'Mi first page class -> '.Config::get('app.STABLE_VERSION'));
        $this->render();
    }
}

// $this->addTwigVars('helloWorld', 'Mi first page controller -> '.Config::get('app.STABLE_VERSION'));
// $this->render();