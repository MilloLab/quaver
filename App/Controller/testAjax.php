<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Config;

$result = array(
    'success' => false,
);

$id = (int) $_REQUEST['id'];
if ($id == 1) {
    $result['success'] = true;
}

$this->respondAjaxRequest($result['success']);
