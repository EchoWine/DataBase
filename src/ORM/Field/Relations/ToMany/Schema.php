<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToMany;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	/**
	 * Reference
	 *
	 * @var
	 */
	public $resolver;

	/**
	 * Tmp
	 *
	 * @var
	 */
	public $tmp;

	/**
	 * Include field in toArray operations
	 *
	 * @var bool
	 */
	public $enable_to_array = false;


	/**
	 * Alter
	 */
	public function alter($table){

	}

	/**
	 * Construct
	 */
	public function __construct($name = null,$resolver_end_model,$resolver_end_field){
		$this -> name = $name;
		$this -> label = $name;
		$this -> tmp['resolver_end_model'] = $resolver_end_model;
		$this -> tmp['resolver_end_field'] = $resolver_end_field;
		return $this;
	}

	/**
	 * Get resolver
	 */
	public function getResolver(){
		return $this -> resolver;
	}

	public function boot(){
		$resolver = new Resolver($this -> getObjectClass(),$this -> tmp['resolver_end_model']);

		
		if($this -> tmp['resolver_end_field']){
			$resolver -> end -> field_to_start = $resolver -> end -> schema -> getField($this -> tmp['resolver_end_field']);
		}

		if(!$resolver -> end -> field_to_start)
			throw new \Exception($this -> getObjectClass().".".$this -> getName().": ToMany: field ".$this -> tmp['resolver_end_field']." cannot be null");
		
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