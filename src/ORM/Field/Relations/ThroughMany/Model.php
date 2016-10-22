<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ThroughMany;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * has value raw
	 */
	public $has_value_raw = false;

	/**
	 * Is the value updated
	 *
	 * @var bool
	 */
	public $value_updated = false;

	/**
	 * Set value
	 */
	public function setValueOut($value = null){

		if($value == null)
			$value = [];


		$c = new Collection($value);

		$c -> setModel($this);

		$this -> value_out = $c;
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

		return [];
	}


	/**
	 * Retrieve a value out given a value raw
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueOutByValueRaw($value){

		$resolver = $this -> getSchema() -> getResolver();
		
		# Field start Model
		$start_field_identifier = $this -> getObjectModel() -> getField($resolver -> start -> field_identifier -> getName());


		# Get all result of resolver: Mid table
		if(!$start_field_identifier -> getValue()){

			$this -> value_updated = false;
			return null;
		}

		$pivots = $resolver -> mid -> model::where($resolver -> mid -> field_to_start -> getColumn(),$start_field_identifier -> getValue()) 
		-> setIndexResult($resolver -> mid -> field_to_end -> getColumn())
		-> get();

		# Get all result of relation: End table
		$results = $pivots -> select($resolver -> mid -> field_to_end -> getColumn());

		if($results -> count() != 0)
			$results = $resolver -> end -> model::whereIn($resolver -> end -> field_identifier -> getColumn(),$results -> toArray()) -> get();

		$end_identifier = $resolver -> end -> field_identifier -> getName();

		foreach($results as $result){
			$result -> pivot = $pivots[$result -> {$end_identifier}];
		}

		return $results;

	}

	public function checkInstanceValueClass($model){
		if(get_class($model) != $this -> getSchema() -> getResolver() -> end -> model){
			throw new \Exception($this -> getSchema() -> getResolver() -> end -> model." != ".get_class($model));
		}
	}

	/**
	 * Get the value
	 *
	 * @return mixed
	 */
	public function getValue(){
		

		if(($this -> getValueOut() == null || $this -> getValueOut() -> count() == 0) && !$this -> value_updated){

			

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
		return array_map(function($model){
			return $model -> getPrimaryField() -> getValue();
		},(array)$this -> getValue());
	}
}
?>