<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToOne;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	public $value_updated = true;

	/**
	 * Set Model
	 */
	public function iniAlias(){
		$this -> alias = [$this -> getSchema() -> getColumn(),$this -> getSchema() -> getName()];
	}

	/**
	 * Set the value raw by repository
	 *
	 * @return mixed
	 */
	public function setValueRawFromRepository($value_raw,$persist = false,$relations = []){
		$value_raw = isset($value_raw[$this -> getSchema() -> getColumn()]) ? $value_raw[$this -> getSchema() -> getColumn()] : null;

		$this -> value_raw = $value_raw;
		$value = null;
		
		if(isset($relations[$this -> getSchema() -> getRelation()])){

			foreach($relations[$this -> getSchema() -> getRelation()] as $rel){
				if($rel -> getFieldByColumn($this -> getSchema() -> getRelationColumn()) == $value_raw){
					$value = $rel;
					break;
				}
			}
		}
		
		if(!$persist){
			
			$this -> setValue($this -> parseRawToValue($value),false);
			$this -> persist = $persist;
		}
	}

	/**
	 * Set the value raw
	 *
	 * @return mixed
	 */
	public function setValueRawToRepository($value_raw,$persist = false){


		if($this -> getLastAliasCalled() == $this -> getSchema() -> getName()){

			if($value_raw !== null)
				$value_raw = $value_raw -> getFieldByColumn($this -> getSchema() -> getRelationColumn()) -> getValue();
		}
		
		$this -> value_raw = $value_raw;

		if(!$persist){
			$this -> setValue($this -> parseRawToValue($value_raw),false);
			$this -> persist = $persist;
		}
	}


	/**
	 * Get the value raw
	 *
	 * @return mixed
	 */
	public function getValueRaw(){
		return $this -> value_raw;
	}


	/**
	 * Set the value
	 *
	 * @param mixed $value
	 * @param bool $persist
	 */
	public function setValue($value = null,$persist = true){
		if($this -> getLastAliasCalled() == $this -> getSchema() -> getColumn()){
			$this -> setValueRawToRepository($value,true);
			$this -> value = null;
			$this -> value_updated = false;
			$this -> persist = $persist;
			return;
		}

		$this -> value = $value;

		if($persist){
			$this -> setValueRawToRepository($this -> parseValueToRaw($value),true);
			$this -> persist = $persist;
		}
	}


	/**
	 * Get the value
	 *
	 * @return mixed
	 */
	public function getValue(){

		if($this -> getLastAliasCalled() == $this -> getSchema() -> getColumn())
			return $this -> getValueRaw();

		if(!$this -> value_updated){

			$this -> value = $this -> getSchema() -> getRelation()::first($this -> getValueRaw());
			$this -> value_updated = true;

		}

		return $this -> value;
	}

	/**
	 * Get the value
	 *
	 * When ORM\Model::toArray is called, this return the value of field
	 *
	 * @return mixed
	 */
	public function getValueToArray(){
		return $this -> getValueRaw();
	}

	/**
	 * Get the name used in array
	 *
	 * When ORM\Model::toArray is called, this return the name of field
	 *
	 * @return string
	 */
	public function getNameToArray(){
		return $this -> getSchema() -> getColumn();
	}

}
?>