<?php
$commands[] = array('command'=>'model [name] [dbtable]','description'=>'Generate a model');

function model($module, $dbtable) {
	if (trim($module) == '' || trim($dbtable) == '') {
		die("Provide a module and a database table name, please.\n");
	}
	$className = str_replace(" ","",ucwords(str_replace("_"," ",$module)));
	echo "Generating $className...\n";
		$contents="<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Model;

use Quaver\Core\DB;
use Quaver\Core\Model;

/**
 * $className class
 * @package App
 */
class $className extends Model
{
	protected \$table = '$dbtable';

}";
	$path = MODEL_PATH;
	if(!file_exists($path."/$className.php")){
		file_put_contents($path."/$className.php", $contents);
		echo "Complete.\n";
	} else {
		echo "File already $className.php exists!\n";
	}
}