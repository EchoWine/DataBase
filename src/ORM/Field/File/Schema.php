<?php

namespace CoreWine\DataBase\ORM\Field\File;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	protected $dir_object = null;
	protected $dir_model = null;
	protected $filesystem;

	protected static $default_path_web = "";
	protected static $default_path_file = "";


	public static function setDefaultWebPath($dir){
		self::$default_path_web = $dir;
	}

	public static function setDefaultFilePath($dir){
		self::$default_path_file = $dir;
	}

	protected $thumbs = [];

	public function thumb($name,$info){
		$info['name'] = $name;
		$this -> thumbs[$name] = $info;
	}

	public function getThumbs(){
		return $this -> thumbs;
	}

	public function getThumb($name){
		return isset($this -> thumbs[$name]) ? $this -> thumbs[$name] : null;
	}

	public function filesystem($filesystem){
		$this -> filesystem = $filesystem;
	}

	public function callFilesystem($model){
		$c = $this -> filesystem;

		$object = $model -> getObjectModel();

		if($c === null){
			return $this -> getObjectSchema() -> getTable()."/".$object -> id."/".$model -> getValue();
		}

		return $c($object);
	}
	

	public function dirModel($dir_model){
		$this -> dir_model = $dir_model;
		return $this;
	}

	public function getDirModel($model){
		return $this -> dir_model == null ?  : $this -> dir_model;
	}

	public function getDirObject(){
		return $this -> dir_object == null ?  : $this -> dir_object;
	}

	public function getPathFile(){
		return self::$default_path_file;
	}

	public function getPathWeb(){
		return self::$default_path_web;
	}
}

?>