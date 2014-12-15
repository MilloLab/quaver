<?php
$commands[] = array('command'=>'skeleton [name]','description'=>'Generate a skeleton: model, controller and view');

function skeleton($name)
{
	$moduleName = slugify($name);

	if (is_dir(MODEL_PATH."/$moduleName")) {
		die("Module already exists! \n");
	}

	$startTime = time();

	echo "\n\nGenerating skeleton $moduleName... \n\n";
	sleep(1);

	//create Controller
	echo "Creating Controller... \n";
	generateController(CONTROLLER_PATH, $moduleName);

	//create Model
	echo "Creating Model... \n";
	generateModuleModel(MODEL_PATH, $moduleName);

	//create View
	echo "Creating View... \n";
	generateView(VIEW_PATH, $moduleName);

	$endTime = time();

	echo $moduleName." created \n";
	$time = number_format($startTime - $endTime,5);
	echo "Complete.\n\n";

	echo "Check your Routes.yml to add this new module!!!\n\n";
}


function generateController($path,$moduleName)
{
	$contents="<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Controller;

use Quaver\Model\Functions;

\$template = \$this->twig->loadTemplate('$moduleName.twig');
echo $template->render(\$this->twigVars);";
	if (is_dir($path)) {
		file_put_contents($path."/$moduleName.php", $contents);
	}
}

function generateModuleModel($path, $moduleName)
{
	$contents="<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Model;

use Quaver\Core\DB;

class $moduleName extends Base
{
	public \$_table = '$moduleName';

}";
	if (is_dir($path)) {
		file_put_contents($path."/$moduleName.php", $contents);
	}
}


function generateView($path,$moduleName) 
{
	$contents="Hello, {{ $moduleName }}!";
	if (is_dir($path)) {
		file_put_contents($path."/$moduleName.twig", $contents);
	}
}

function slugify($text)
{ 
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}