<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Config;

$this->addTwigVars('helloWorld', 'Mi first page -> '.Config::get('app.STABLE_VERSION'));
$this->render();
