<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

if ($_user->logged){
	$_user->unsetCookie();	
}

if (isset($_GET['ref'])) {
    $goTo = strip_tags(addslashes($_GET['ref']));
} elseif (isset($_SERVER['HTTP_REFERER'])) {
    $goTo = strip_tags(addslashes($_SERVER['HTTP_REFERER']));
} else {
    $goTo = '/';
}

header("Location: $goTo");
exit;
