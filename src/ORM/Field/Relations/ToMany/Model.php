<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToMany;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * has value raw
	 */
	public $has_value_raw = false;

	/** 
	 * List of all Model to save()
	 */
	public $value_to_save = [];

	/** 
	 * List of all Model to delete()
	 */
	public $value_to_remove = [];

	/** 
	 * List of all Model to delete()
	 */
	public $value_to_delete = [];

	/**
	 * Last value relation 
	 *
	 * @var mixed
	 */
	protected $last_value_relation = [];

	/**
	 * Is the value updated
	 *
	 * @var bool
	 */
	public $value_updated = false;


	/**
	 * Add a model to collection if isn't already added
	 *
	 * @param ORM\Model $model
	 */
	public function add($model){

		$model -> getFieldByColumn($this -> getSchema() -> getReference()) -> setValue($this -> getModel());
		$this -> addValue($model);
		$this -> addValueToSave($model);
	}

	/**
	 * Add model in value
	 *
	 * @param ORM\Model $model
	 */
	public function addValue($model){
		$index = count($this -> value);
		$this -> value[$index] = $model;
	}

	/**
	 * Remove model in value
	 *
	 * @param ORM\Model $model
	 */
	public function removeValue($index){
		unset($this -> value[$index]);
	}

	/**
	 * Add to model to save
	 *
	 * @param ORM\Model $model
	 */
	public function addValueToSave($model){
		$this -> value_to_save[$model -> getPrimaryField() -> getValue()] = $model;
	}

	/**
	 * Get list of all model to save
	 *
	 * @return array ORM\Model
	 */
	public function getValueToSave(){
		return $this -> value_to_save;
	}

	/**
	 * Add to model to delete
	 *
	 * @param ORM\Model $model
	 */
	public function addValueToDelete($model){
		$this -> value_to_delete[$model -> getPrimaryField() -> getValue()] = $model;
	}

	/**
	 * Get list of all model to delete
	 *
	 * @return array ORM\Model
	 */
	public function getValueToDelete(){
		return $this -> value_to_delete;
	}


	/**
	 * Add to model to delete
	 *
	 * @param ORM\Model $model
	 */
	public function addValueToRemove($model){
		$this -> value_to_remove[$model -> getPrimaryField() -> getValue()] = $model;
	}

	/**
	 * Get list of all model to delete
	 *
	 * @return array ORM\Model
	 */
	public function getValueToRemove(){
		return $this -> value_to_remove;
	}

	/**
	 * Remove a model to collection if exist
	 *
	 * @param ORM\Model $model
	 */
	public function remove($model){

        if($this -> getSchema() -> getCollection()){
            return $this -> delete($model);
        }   

		foreach($this -> getValue() as $n => $_model){
			if($model -> isEqual($_model)){
				$_model -> getFieldByColumn($this -> getSchema() -> getReference()) -> setValue(null);
				$this -> addValueToRemove($model);
				$this -> removeValue($n);
			}
		}
	}

	/**
	 * Remove a model to collection if exist
	 *
	 * @param ORM\Model $model
	 */
	public function delete($model){

        $schema = $this -> getSchema();
		$collection = $schema -> getCollection();
		$relation = $schema -> getRelation();
        $ob = new $relation();
        $ob -> {$collection} = $model;
        $field = $relation::schema() -> getFieldByColumn($schema -> getReference());
        $ob -> {$field -> getName()} = $this -> getModel();
        $model = $ob; 
		foreach($this -> getValue() as $n => $_model){
    		if($_model -> {$collection} == $model -> {$collection} && $_model -> {$field -> getName()} == $model -> {$field -> getName()}){
    				
				$this -> addValueToDelete($_model);
				$this -> removeValue($n);
    		}
    	}
    
	}

	/**
	 * Save all model in collection
	 */
	public function save(){
		foreach($this -> getValueToRemove() as $value){
			$value -> save();
		}

		foreach($this -> getValueToDelete() as $value){
			$value -> delete();
		}

		foreach($this -> getValueToSave() as $value){
			$field = $this -> getSchema() -> getReference();
			$value -> {$field} = $this -> getModel() -> getPrimaryField() -> getValue();
			$value -> save();
		}

	}

	/**
	 * Set the value raw by repository
	 *
	 * @return mixed
	 */
	public function setValueRawFromRepository($row,$persist = false,$relations = []){
		
		# In this case $row and $value_raw is useless, because the entire value is retrieved using relations
		$this -> value_raw = null;

		$value = [];

		$relation = $this -> getSchema() -> getRelation();

		if(isset($relations[$relation])){

			foreach($relations[$relation] as $result){

				foreach($result -> getFields() as $field){

					if($field -> getSchema() -> getType() == 'to_one'){


						if(!$this -> getModel() -> getPrimaryField()){
							print_r($this -> getModel());
							die('...');
						}

						# Of all results take only with a relation, with a column reference, with a value of primary == reference
						if($this -> isThisRelation($field,$result)){
							$value[$result -> getPrimaryField() -> getValue()] = $result;
						}

						$this -> value_updated = true;
					}
				}
			}
		}

		if(!$persist){
			
			$this -> setValue($this -> parseRawToValue($value),false);
			$this -> persist = $persist;
		}
	}

	/**
	 * Given the field and the result return if this is the relation
	 * In order to connect two entities "toMany" i need to search all fields
	 * Search for the field that have column name == $reference and have the value raw same as this primary value 
	 *
	 * @param $field is the field of the Model that maybe is connected
	 * @param $result
	 *
	 * @return bool
	 */
	public function isThisRelation($field,$result){
		
		# The name of column of relation
		$reference = $this -> getSchema() -> getReference();

		# The primary field of this model
		$model_primary = $this -> getModel() -> getPrimaryField();

		# Column name of the $field
		$field_column = $field -> getSchema() -> getColumn();
		
		# The relational field of the result
		$result_reference = $result -> getField($reference);

		# Example
		# "author_id" () == "author_id" && 1 == 1
		return $reference == $field_column && $model_primary -> getValueRaw() == $result_reference -> getValueRaw();					
	}

	/**
	 * Set the value raw
	 *
	 * @return mixed
	 */
	public function setValueRawToRepository($value_raw,$persist = false){

		$this -> value_raw = null;

		if(!$persist){
			$this -> setValue($this -> parseRawToValue($value_raw),false);
			$this -> persist = $persist;
		}
	}

	/**
	 * Parse the value from raw to value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function parseRawToValue($value){


		$c = new Collection($value);

		$c -> setModel($this);
		return $c;
	}

	/**
	 * Get the value
	 *
	 * @return mixed
	 */
	public function getValue(){
		
		if(!$this -> value_updated){

			# The name of column of relation
			$reference = $this -> getSchema() -> getReference();
			
			# The primary field of this model
			$model_primary = $this -> getModel() -> getPrimaryField();

			$v = $this -> getSchema() -> getRelation()::where($reference,$model_primary -> getValue()) -> get();
			$v = new Collection($v);
			$v -> setModel($this);
			$this -> value = $v;
			$this -> value_updated = true;

		}


		return $this -> value;
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