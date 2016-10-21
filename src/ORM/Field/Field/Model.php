<?php

namespace CoreWine\DataBase\ORM\Field\Field;

class Model{
	
	/**
	 * Schema
	 */
	public $schema;

	/**
	 * Model
	 */
	public $object_model;

	/**
	 * has value raw
	 */
	public $has_value_raw = true;

	/**
	 * Value out
	 */
	public $value_out;

	/**
	 * Value raw
	 */
	public $value_raw;

	/**
	 * Persist
	 */
	public $persist;

	/**
	 * Alias
	 */
	public $alias = [];

	/**
	 * Last alias called
	 */
	public $last_alias_called;

	/**
	 * Construct
	 */
	public function __construct($schema,$value_out){
		$this -> schema = $schema;
		$this -> value_out = $value_out;
		$this -> persist = $schema -> persist;
	}

	/**
	 * Set Model
	 */
	public function setObjectModel($model){
		$this -> object_model = $model;
		$this -> iniAlias();
		$model -> setField($this -> getSchema() -> getName(),$this);
	}

	/**
	 * Return the model
	 */
	public function getObjectModel(){
		return $this -> object_model;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType(){
		$this -> getSchema() -> getType();
	}


	/**
	 * Initialize alias
	 */
	public function iniAlias(){
		$this -> alias = [$this -> getSchema() -> getName()];
		$this -> getSchema() -> alias = $this -> alias;
	}

	/**
	 * Get last alias called
	 */
	public function getLastAliasCalled(){
		return $this -> last_alias_called;
	}

	/**
	 * Check if alias exists
	 */
	public function isAlias($alias){
		$this -> last_alias_called = $alias;
		return in_array($alias,$this -> getAlias());
	}

	/**
	 * Get all alias
	 */
	public function getAlias(){
		return $this -> alias;
	}

	/**
	 * Is primary
	 *
	 * @return bool
	 */
	public function isPrimary(){
		return $this -> getSchema() -> getPrimary();
	}

	/**
	 * Is unique
	 *
	 * @return bool
	 */
	public function isUnique(){
		return $this -> getSchema() -> getUnique();
	}

	/**
	 * Is Autoincrement
	 *
	 * @return bool
	 */
	public function isAutoIncrement(){
		return $this -> getSchema() -> getAutoIncrement();
	}

	/**
	 * Get the schema
	 *
	 * @return ORM\Field\Schema
	 */
	public function getSchema(){
		return $this -> schema;
	}

	/**
	 * Set persist
	 *
	 * @return bool
	 */
	public function setPersist($persist = false){
		$this -> persist = $persist;
	}

	/**
	 * Get persist
	 *
	 * @return bool
	 */
	public function getPersist(){
		return $this -> persist;
	}

	/**
	 * Has the value raw
	 *
	 * @return mixed
	 */
	public function hasValueRaw(){
		return $this -> has_value_raw;
	}

	/**
	 * Set the value raw
	 *
	 * @param mixed $value_raw
	 */
	public function setValueRaw($value_raw){
		$this -> value_raw = $value_raw;
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
	 */
	public function setValueOut($value_out = null){
		$this -> value_out = $value_out;

	}

	/**
	 * Get the value
	 *
	 * @return mixed
	 */
	public function getValueOut(){
		return $this -> value_out;
	}

	/**
	 * Set value
	 */
	public function setValue($value){
		return $this -> setValueByValueOut($value);
	}

	/**
	 * Get value
	 */
	public function getValue(){
		return $this -> getValueOut();
	}


	/**
	 * Retrieve a value raw given a value out
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueRawByValueOut($value){
		return $value;
	}

	/**
	 * Retrieve a value raw given a value out
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueOutByValueRaw($value){
		return $value;
	}


	/**
	 * Set the value from repository
	 *
	 * @return mixed
	 */
	public function setValueByValueRaw($value_raw){

		$value_out = $this -> getValueOutByValueRaw($value_raw);

		$this -> setValueRaw($value_raw);
		$this -> setValueOut($value_out);
	}

	/**
	 * Set the value from repository
	 *
	 * @return mixed
	 */
	public function setValueByValueOut($value_out){

		$value_raw = $this -> getValueRawByValueOut($value_out);

		$this -> setValueRaw($value_raw);
		$this -> setValueOut($value_out);
	}

	/**
	 * get the value raw from repository
	 *
	 * @return mixed
	 */
	public function setValueFromRepository($row,$relations = []){
		
		$value_raw = $this -> getValueRawFromRepository($row,$relations);
		$this -> setValueRaw($value_raw);

		$value_out = $this -> getValueOutFromRepository($row,$relations);
		$this -> setValueOut($value_out);
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
		
		return $this -> getValueOutByValueRaw($this -> value_raw);
	}

	/**
	 * Set value copied
	 *
	 * @param mixed $value
	 */
	public function setValueCopied($value = null){

		if(!$this -> getSchema() -> isCopy())
			return;

		if($this -> isUnique()){
			$i = 0;
			do{
				$value_copied = $this -> parseValueCopy($value,$i++);
				$res = $this -> getObjectModel() -> getRepository() -> exists([$this -> getSchema() -> getColumn() => $value_copied]);
				
			}while($res);

			$value = $value_copied;
		}

		$this -> setValue($value);
	}

	/**
	 * Get the value used in array
	 *
	 * When ORM\Model::toArray is called, this return the value of field
	 *
	 * @return mixed
	 */
	public function getValueToArray(){
		return $this -> getValue();
	}
	
	/**
	 * Get the name used in array
	 *
	 * When ORM\Model::toArray is called, this return the name of field
	 *
	 * @return string
	 */
	public function getNameToArray(){
		return $this -> getSchema() -> getName();
	}

	/**
	 * Parse the value for edit
	 *
	 * @param mixed $value
	 * @param int $i count
	 *
	 * @return value parsed
	 */
	public function parseValueCopy($value,$i){
		return $value."_".$i;
	}

	/**
	 * To string
	 */
	public function __tostring(){
		return $this -> getValue();
	}

	/**
	 * Add the field to query to add an model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function addRepository($repository){
		return $repository -> addInsert($this -> getSchema() -> getColumn(),$this -> getValueRaw());
	}

	/**
	 * Add the field to query to edit an model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function editRepository($repository){
		return $repository -> addUpdate($this -> getSchema() -> getColumn(),$this -> getValueRaw());
	}

	/**
	 * Add the field to query to find the model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function whereRepository($repository){
		return $repository -> where($this -> getSchema() -> getColumn(),$this -> getValueRaw());
	}

	/**
	 * Check persist
	 *
	 * Re-check the current status of persist. This function is called before save() to check which field put in query
	 */
	public function checkPersist(){}

	/**
	 * Define events callback
	 *
	 * @return array
	 */
	public function events(){
		return [];
	}

}
?>