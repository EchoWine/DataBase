<?php

namespace CoreWine\DataBase\ORM;

use CoreWine\Component\App;

class Application extends App{

	public function __construct(){
		SchemaBuilder::setFields(include PATH_CONFIG.'/orm.php');
	}

	public function app(){}

}

?>