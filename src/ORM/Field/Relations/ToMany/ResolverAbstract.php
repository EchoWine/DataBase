<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToMany;

class ResolverAbstract{

	public $model;
	public $table;

    public function __construct($model){
    	$this -> model = $model;

    	$schema = $model::schema();
    	$this -> schema = $schema;
    	$this -> table = $schema -> getTable();
		$this -> field_identifier = $schema -> getFieldPrimary();
    }
}