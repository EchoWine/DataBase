<?php

namespace CoreWine\DataBase\ORM\Field\Collection;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * Value out
	 */
	public $value_out = [];

	/**
	 * Retrieve a value raw given a value out
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueRawByValueOut($value){
		
		return json_encode($value);
	}

	/**
	 * Retrieve a value out given a value raw
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueOutByValueRaw($value){

		return json_decode($value,true);
	}
	
	/**
	 * Set value
	 */
	public function setValueOut($value = null){

		if($value == null)
			$value = [];

		$c = new Collection($value);

		$c -> setModel($this);

		parent::setValueOut($c);
	}

	/**
	 * Get value of this model when it's called toArray
	 *
	 * @return Array
	 */
	public function getValueToArray(){

		$r = $this -> getValue();

		return $r;
	}

}
?>