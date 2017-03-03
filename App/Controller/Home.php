<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Controller;

/**
 * Class Home
 * @package Quaver\App\Controller
 */
class Home extends Controller
{
    /**
     *
     */
    public function indexAction()
    {
        $this->addTwigVars('helloWorld', 'Mi first page class -> '.$this->getContainer()->get('config')->val('app.STABLE_VERSION'));
        $this->render();
    }
}
