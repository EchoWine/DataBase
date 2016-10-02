<?php

namespace CoreWine\DataBase\ORM\Field\Datetime;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{
	
	/**
	 * Alter
	 */
	public function alter($table){
		$table -> datetime($this -> getColumn());
	}

}

?>