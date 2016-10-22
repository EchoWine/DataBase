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

	protected $collection;


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

	public function to($name,$relation_name){
		$this -> name = $name;
		$this -> label = $name;
		$this -> collection = $relation_name;
	}

	public function getCollection(){
		return $this -> collection;
	}

	/**
	 * Resolve relations
	 *
	 * @param array $result
	 * @param Repository $repository
	 *
	 * @return array
	 */
	public function resolveRelations(&$results,&$relation,$repository){



		$field_relation = null;

		# Search the field that is relationated with this schema

		foreach($this -> getRelation()::schema() -> getFields() as $_field_relation){
			if($_field_relation -> getType() == 'to_one'){
				if($_field_relation -> getRelation() == $repository -> getModel() && $this -> getReference() == $_field_relation -> getColumn()){
					
					$field_relation = $_field_relation;
				}
			}
		}

		if($field_relation !== null){
			foreach($results as $result){
				
				$relation[$this -> getRelation()][$field_relation -> getColumn()][$result[$this -> getObjectSchema() -> getPrimaryColumn()]] = $result[$this -> getObjectSchema() -> getPrimaryColumn()];
			}
		}

			
	}
}

?>