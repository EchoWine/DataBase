<?php

namespace CoreWine\DataBase\ORM\Field\Timestamp;

use CoreWine\DataBase\ORM\Field\Integer\Schema as IntegerSchema;

class Schema extends IntegerSchema{
	
	/**
	 * Alter
	 */
	public function alter($table){
		$table -> timestamp($this -> getColumn());
	}

}

?>