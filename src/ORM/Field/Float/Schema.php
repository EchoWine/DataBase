<?php

namespace CoreWine\DataBase\ORM\Field\Float;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	/**
	 * Lenght
	 */
	public $max_length = 11;

	/**
	 * Lenght
	 */
	public $min_length = 0;

	/**
	 * Alter
	 */
	public function alter($table){
		$table -> float($this -> getColumn(),$this -> getMaxLength());
	}

}

?>