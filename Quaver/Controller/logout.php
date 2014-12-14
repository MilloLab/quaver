<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Controller;

if ($_user->logged){
	$_user->unsetCookie();	
}

if (!empty($_GET['ref'])) {
    $goTo = strip_tags(addslashes($_GET['ref']));
} elseif (!empty($_SERVER['HTTP_REFERER'])) {
    $goTo = strip_tags(addslashes($_SERVER['HTTP_REFERER']));
} else {
    $goTo = '/';
}

header("Location: $goTo");
exit;
