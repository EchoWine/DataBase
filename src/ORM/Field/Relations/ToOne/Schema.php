<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToOne;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	/**
	 * Name of model of Relation
	 */
	public $relation;

	/**
	 * Name of column relation
	 */
	public $relation_column;

	/**
	 * Set relation
	 *
	 * @param String $relation
	 */
	public function relation($relation,$column = null,$relation_column = null){
		$this -> relation = $relation;


		if($relation_column == null){
			$relation_column = $relation::schema() -> getPrimaryField() -> getColumn();
		}

		if($column == null){
			$column = $this -> getName()."_".$relation_column;
		}

		$this -> column = $column;
		$this -> relation_column = $relation_column;

		return $this;
	}

	/**
	 * Get relation
	 */
	public function getRelation(){
		return $this -> relation;
	}

	/**
	 * Get relation
	 */
	public function getRelationColumn(){
		return $this -> relation_column;
	}


	/**
	 * Construct
	 */
	public function __construct($relation = null,$name = null,$column = null,$relation_column = null){
		$this -> name = $name;
		$this -> label = $name;


		$this -> relation($relation,$column,$relation_column);
		return $this;
	}

	/**
	 * New
	 */
	public static function factory($relation = null,$name = null,$column = null,$relation_column = null){
		return new static($relation,$name,$column,$relation_column);
	}
}

?>