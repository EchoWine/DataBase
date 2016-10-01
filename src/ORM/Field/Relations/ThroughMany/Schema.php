<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToMany;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{
	
	/**
	 * Name of model of Relation
	 *
	 * @var
	 */
	public $relation;

	/**
	 * Reference
	 *
	 * @var
	 */
	public $reference;

	/**
	 * Include field in toArray operations
	 *
	 * @var bool
	 */
	public $enable_to_array = false;

	/**
	 * Set relation
	 *
	 * @param String $relation
	 */
	public function relation($relation){
		$this -> relation = $relation;
		$this -> column = null;
		return $this;
	}

	/**
	 * Get relation
	 */
	public function getRelation(){
		return $this -> relation;
	}

	/**
	 * Set reference
	 *
	 * @param String $reference
	 */
	public function reference($reference){
		$this -> reference = $reference;
		return $this;
	}

	/**
	 * Get reference
	 */
	public function getReference(){
		return $this -> reference;
	}

	/**
	 * Alter
	 */
	public function alter($table){

	}

	/**
	 * Construct
	 */
	public function __construct($relation = null,$name = null,$reference = null){
		$this -> name = $name;
		$this -> label = $name;
		$this -> relation($relation);
		$this -> reference($reference);
		return $this;
	}

	/**
	 * New
	 */
	public static function factory($relation = null,$name = null,$reference = null){
		return new static($relation,$name,$reference);
	}

}

?>