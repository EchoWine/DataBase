<?php

namespace CoreWine\DataBase\ORM\Field\File;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	protected $dir_base = null;
	protected $dir_object = null;
	protected $dir_model = null;

	public function dirModel($dir_model){
		$this -> dir_model = $dir_model;
	}

	public function dirBase($dir_base){
		$this -> dir_base = $dir_base;
	}

	public function getDirModel($model){
		return $this -> dir_model == null ? $model -> id."/" : $this -> dir_model;
	}

	public function getDirObject(){
		return $this -> dir_object == null ? $this -> getObjectSchema() -> getTable()."/" : $this -> dir_object;
	}

	public function getDirBase(){
		return $this -> dir_base == null ? "img/" : $this -> dir_base;
	}

}

?>