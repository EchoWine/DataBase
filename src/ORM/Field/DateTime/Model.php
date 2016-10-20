<?php

namespace CoreWine\DataBase\ORM\Field\DateTime;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * Set the value
	 *
	 * @param mixed $value
	 */
	public function setValue($value = null,$persist = true){

		if(is_object($value)){

			if(!$value instanceof \DateTime){

			}

			$value_raw = $value -> format('Y-m-d H:i:s');
		}else{

			$value_raw = $value;
			$value = \DateTime::createFromFormat('Y-m-d H:i:s',$value_raw);

		}

		$this -> value = $value;

		if($persist){
			$this -> setValueRawToRepository($this -> parseValueToRaw($value_raw),true);
			$this -> persist = $persist;
		}
	}

}
?>