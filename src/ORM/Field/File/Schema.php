<?php

namespace CoreWine\DataBase\ORM\Field\File;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	protected $dir_object = null;
	protected $dir_model = null;

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

	public function dirModel($dir_model){
		$this -> dir_model = $dir_model;
		return $this;
	}

	public function getDirModel($model){
		return $this -> dir_model == null ? $model -> id."/" : $this -> dir_model;
	}

	public function getDirObject(){
		return $this -> dir_object == null ? $this -> getObjectSchema() -> getTable()."/" : $this -> dir_object;
	}

	public function getPathFile(){
		return self::$default_path_file;
	}

	public function getPathWeb(){
		return self::$default_path_web;
	}
}

?>