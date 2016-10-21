<?php

namespace CoreWine\DataBase\ORM\Field\Relations\BelongsToOne;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * has value raw
	 *
	 * @param boolean
	 */
	public $has_value_raw = false;

	public $value_updated = false;

	/**
	 * Retrieve a value raw given a value out
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueRawByValueOut($value){

		return null;
	}

	/**
	 * Set value out
	 *
	 * @param mixed $value
	 */
	public function setValueOut($value = null){


		if(!$value)
			return;

		$field = $this -> getSchema() -> getFieldRelationByModel($value);

		if(!$field){
			throw new \Exception("Couldn't find field of relations BelongsToOne");
		}

		# Add "stack to persist"
		# Remove "old relation"
		$old_value = $this -> getObjectModel() -> getField($this -> getSchema() -> getName()) -> getValue();
		if($old_value){
			$old_value -> {$field} = null;
		}


		# Add new relation
		$value -> {$field} = $this -> getObjectModel();


		# $value -> {$field} Remove connection;

		$this -> value_out = $value;
	}

	/**
	 * Retrieve a value out given a value raw
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueOutByValueRaw($value){

		$relations = $this -> getSchema() -> getRelations();
		$primary_value = $this -> getObjectModel() -> getPrimaryField() -> getValue();

		# Search through all possible relations to find one match
		foreach($relations as $relation_model => $relation_field){
			$field = $relation_model::schema() -> getField($relation_field);

			$relation = $relation_model::where($field -> getColumn(),$primary_value) -> first();

			if($relation){
				return $relation;
			}
		}

		return null;
	}

	/**
	 * get the value raw from repository
	 *
	 * @return mixed
	 */
	public function getValueRawFromRepository($row,$relations = []){
		
		return null;
	}


	/**
	 * get the value raw from repository
	 *
	 * @return mixed
	 */
	public function getValueOutFromRepository($row,$relations = []){

		# One day...
		return null;
	}

	/**
	 * Get the value raw
	 *
	 * @return mixed
	 */
	public function getValueRaw(){
		
		if(is_object($this -> value_raw)){
			die("CoreWine\DataBase\ORM\Field\Relations\BelongsToOne\Model: This is bad");
		}

		return $this -> value_raw;
	}

	/**
	 * Get the value
	 *
	 * @return mixed
	 */
	public function getValue(){

		if(!$this -> getValueOut() && !$this -> value_updated){
				
			$this -> value_updated = true;
			$this -> setValueByValueRaw(null);
		}

		return $this -> getValueOut();
	}


	/**
	 * Add the field to query to add an model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function addRepository($repository){
		return $repository;
	}

	/**
	 * Add the field to query to edit an model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function editRepository($repository){
		return $repository;
	}


	/**
	 * Get the value used in array
	 *
	 * When ORM\Model::toArray is called, this return the value of field
	 *
	 * @return mixed
	 */
	public function getValueToArray(){

		return $this -> getValue() 
			? $this -> getValue() -> getPrimaryField() -> getValue()
			: null;

	}

}
?>