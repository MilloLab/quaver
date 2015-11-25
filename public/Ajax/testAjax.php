<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

require 'index.php';

$result = array(
    'success' => false,
);

$id = (int) $_REQUEST['id'];
if ($id == 1) {
    $result['success'] = true;
}

echo json_encode($result);
