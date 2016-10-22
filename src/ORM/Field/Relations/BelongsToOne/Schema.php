<?php

namespace CoreWine\DataBase\ORM\Field\Relations\BelongsToOne;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	/**
	 * Name of model of Relation
	 */
	public $relations;

	/**
	 * Alter
	 */
	public function alter($table){

	}

	/**
	 * Get relation
	 */
	public function getRelations(){
		return $this -> relations;
	}

	/**
	 * Construct
	 */
	public function __construct($name = null,$relations = null){
		$this -> name = $name;
		$this -> label = $name;
		$this -> relations = $relations;
	}

	/**
	 * Retrieve field relation using model
	 *
	 * @param ORM\Model $model
	 *
	 * @return string
	 */
	public function getFieldRelationByModel($model){
		
		foreach($this -> getRelations() as $relation_model => $relation_field){
			if($relation_model == get_class($model)){
				return $relation_field;
			}
		}

		return null;
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