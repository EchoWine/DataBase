<?php

namespace CoreWine\DataBase\ORM\Field\Collection;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	/**
	 * Alter
	 */
	public function alter($table){
		$col = $table -> text($this -> getColumn(),$this -> getMaxLength());

		if(!$this -> getRequired())
			$col -> null();
	}

	public function validate($value,$values,$model){
		return null;
	}
}

?>