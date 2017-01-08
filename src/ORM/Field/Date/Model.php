<?php

namespace CoreWine\DataBase\ORM\Field\Date;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;
use CoreWine\Component\DateTime;

class Model extends FieldModel{

	
	/**
	 * Retrieve a value raw given a value out
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueRawByValueOut($value){

		if(is_object($value)){

			if(!$value instanceof DateTime){
				# Some errors...
			}

			return $value -> format('Y-m-d');
		}

		# Some errors...
			
		return null;
	}

	/**
	 * Retrieve a value out given a value raw
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueOutByValueRaw($value){

		if($value)
			$value = DateTime::createFromFormat('Y-m-d',$value);

		return $value;
	}

	/**
	 * Set the value
	 *
	 * @param mixed $value
	 */
	public function setValue($value = null){

		if(is_object($value)){

			return $this -> setValueByValueOut($value);


		}
		
		return $this -> setValueByValueRaw($value);
		
	}

	public function getValueToArray(){
		return $this -> getValue() -> format('Y-m-d');
	}

}
?>