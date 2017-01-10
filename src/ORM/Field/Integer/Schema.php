<?php

namespace CoreWine\DataBase\ORM\Field\Integer;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	/**
	 * Lenght
	 */
	public $maxLength = 11;

	/**
	 * Lenght
	 */
	public $minLength = 0;

	/**
	 * Alter
	 */
	public function alter($table){
		$col = $table -> int($this -> getColumn(),$this -> getMaxLength());

		if(!$this -> required)
			$col -> null();

		if($this -> required)
			$col -> notNull();

		if($this -> primary)
			$col -> primary();
		
		if($this -> default)
			$col -> default();
	}

}

?>