<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ThroughMany;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

use CoreWine\DataBase\ORM\Model;

class Schema extends FieldSchema{
	
	/**
	 * Table/Model resolver
	 *
	 * @var
	 */
	public $resolver = null;

	/**
	 * Tmp
	 *
	 * @var
	 */
	public $tmp = [];


	/**
	 * Include field in toArray operations
	 *
	 * @var bool
	 */
	public $enable_to_array = false;

	/**
	 * Construct
	 */
	public function __construct($name = null,$resolver_end_model = null){
		$this -> name = $name;
		$this -> label = $name;
		$this -> column = null;
		$this -> tmp['resolver_end_model'] = $resolver_end_model ;
		return $this;
	}

	public function resolver($resolver_mid_model,$resolver_mid_field_start = null,$resolver_mid_field_end = null){
		$this -> tmp['resolver_mid_model'] = $resolver_mid_model;
		$this -> tmp['resolver_mid_field_start'] = $resolver_mid_field_start;
		$this -> tmp['resolver_mid_field_end'] = $resolver_mid_field_end;
		return $this;
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
		$resolver = new Resolver($this -> getObjectClass(),$this -> tmp['resolver_mid_model'],$this -> tmp['resolver_end_model']);

		# Update resolver
		# Throw exceptions if not found
		$resolver -> mid -> field_to_start = $resolver -> mid -> schema -> getField($this -> tmp['resolver_mid_field_start']);
		$resolver -> mid -> field_to_end = $resolver -> mid -> schema -> getField($this -> tmp['resolver_mid_field_end']);
		
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