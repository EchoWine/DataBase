<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToOne;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * Last value relation 
	 *
	 * @var mixed
	 */
	protected $last_value_relation = null;

	/**
	 * Is the value updated
	 *
	 * @var bool
	 */
	public $value_updated = false;

	/**
	 * Initialze the alis
	 */
	public function iniAlias(){
		$this -> alias = [$this -> getSchema() -> getColumn(),$this -> getSchema() -> getName()];
	}

	/**
	 * Retrieve a value raw given a value out
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueRawByValueOut($value){

		if(is_object($value)){

			if(get_class($value) !== $this -> getSchema() -> getRelation()){

				throw new \Exception(basename($this -> getObjectModel() -> getClass()).
					" Incorrect object assigned to field: ". 
					basename($this -> getSchema() -> getRelation())." != ".basename(get_class($value)));
			}

			if($value)
				return $value -> getFieldByColumn($this -> getSchema() -> getRelationColumn()) -> getValue();
		}

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

		$relation = $this -> getSchema() -> getRelation();
		$relation_column = $this -> getSchema() -> getRelationColumn();

		return $relation::where($relation_column,$value) -> first();
	}

	/**
	 * get the value raw from repository
	 *
	 * @return mixed
	 */
	public function getValueRawFromRepository($row,$relations = []){
		
		return isset($row[$this -> getSchema() -> getColumn()]) ? $row[$this -> getSchema() -> getColumn()] : null;
	}


	/**
	 * get the value raw from repository
	 *
	 * @return mixed
	 */
	public function getValueOutFromRepository($row,$relations = []){

		$value_raw = $this -> getValueRaw();
		$relation = $this -> getSchema() -> getRelation();
		$relation_column = $this -> getSchema() -> getRelationColumn();

		if(isset($relations[$relation])){

			foreach($relations[$relation] as $rel){
				if($rel -> getFieldByColumn($relation_column) -> getValue() == $value_raw){
					return $rel;
				}
			}
		}

		return null;
	}




	/**
	 * Get the value raw
	 *
	 * @return mixed
	 */
	public function getValueRaw(){
		
		# @debug
		if(is_object($this -> value_raw)){
			die("CoreWine\DataBase\ORM\Field\Relations\ToOne\Model: This is bad");
		}

		return $this -> value_raw;
	}

	
	/**
	 * Set the value
	 *
	 * @param mixed $value
	 * @param bool $persist
	 */
	public function setValue($value = null){
		
		if(is_object($value)){
			
			$this -> setValueByValueOut($value);

			if($value){

				$this -> value_updated = true;
				$this -> last_value_relation = $value -> {$this -> getSchema() -> getRelationColumn()};
			}

			return null;
		}

		$this -> value_updated = true;
		$this -> setValueByValueRaw($value);
		return null;


	}


	/**
	 * Get the value
	 *
	 * @return mixed
	 */
	public function getValue(){


		if($this -> getLastAliasCalled() == $this -> getSchema() -> getColumn())
			return $this -> getValueRaw();

		
		if(!$this -> value_updated && $this -> getValueRaw()){

			$r = $this -> getValueOutByValueRaw($this -> getValueRaw());
			$this -> setValueOut($r);
			$this -> value_updated = true;

		}

		return $this -> getValueOut();
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