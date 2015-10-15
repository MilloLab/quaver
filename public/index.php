<?php

require_once('bootstrap.php');

$app = new \Quaver\Core\App();

// My composer quaver-plugins
$app->addModule('HelloWorld', 'millolab/quaver-helloworld');
$app->addModule('Mail', 'millolab/quaver-mail');

$app->run();