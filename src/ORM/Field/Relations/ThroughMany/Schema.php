<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ThroughMany;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

use CoreWine\DataBase\ORM\Model;

class Schema extends FieldSchema{
	
	/**
	 * Name of model of Relation
	 *
	 * @var
	 */
	public $relation;
	
	/**
	 * Table/Model resolver
	 *
	 * @var
	 */
	public $resolver = null;

	/**
	 * Table/Model resolver
	 *
	 * @var
	 */
	public $resolver_field_start = null;

	/**
	 * Table/Model resolver
	 *
	 * @var
	 */
	public $resolver_field_end = null;

	/**
	 * Include field in toArray operations
	 *
	 * @var bool
	 */
	public $enable_to_array = false;

	/**
	 * Construct
	 */
	public function __construct($name = null,$relation = null){
		$this -> name = $name;
		$this -> label = $name;
		$this -> column = null;
		$this -> relation = $relation;
		return $this;
	}

	public function resolver($model,$resolver_field_start = null,$resolver_field_end = null){
		$this -> resolver = $model;
		$this -> resolver_field_start = $resolver_field_start;
		$this -> resolver_field_end = $resolver_field_end;
		return $this;
	}

	/**
	 * Get relation
	 */
	public function getRelation(){
		return $this -> relation;
	}

	/**
	 * Get resolver
	 */
	public function getResolver(){
		return $this -> resolver;
	}

	/**
	 * Alter
	 */
	public function alter($table){

		# Parse resolver

		# Create mid table if doesn't exist
		# Only if resolver is not defined

	}

	public function boot(){
		$resolver = new Resolver($this -> getObjectClass(),$this -> resolver,$this -> relation);

		# Update resolver
		# Throw exceptions if not found
		$resolver -> mid -> field_to_start = $this -> resolver::schema() -> getField($this -> resolver_field_start);
		$resolver -> mid -> field_to_end = $this -> resolver::schema() -> getField($this -> resolver_field_end);
		
		if(!$resolver -> mid -> field_to_start)
			throw new \Exception("ThroughMany: field to start cannot be null");
		
		if(!$resolver -> mid -> field_to_end)
			throw new \Exception("ThroughMany: field to end cannot be null");

		$this -> resolver = $resolver;
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

	}
}

?>