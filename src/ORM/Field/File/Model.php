<?php

namespace CoreWine\DataBase\ORM\Field\File;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	public $content;
	public $filename;

	public function setByUrl($url,$filename = null){
		
		if($filename == null){
			$filename = basename($url);
		}

		return $this -> setByContent(file_get_contents($url),$filename);

	}

	public function setByContent($content,$filename){

		# Check for resizing...
		# Check for extension...
		$this -> content = $content;
		$this -> filename = $filename;

		$this -> setValue($filename);
	}

	public function getContentByFilename($filename){
		return file_get_contents($filename);
	}

	public function getDirBase(){
		return $this -> getSchema() -> getDirBase();
	}

	public function getDirObject(){
		return $this -> getSchema() -> getDirObject();
	}

	public function getDirModel($model){
		return $this -> getSchema() -> getDirModel($model);
	}

	/**
	 * Define events callback
	 *
	 * @return array
	 */
	public function events(){

		return [
			'saved' => function($model){

				if(!$this -> content)
					return;
				
				$table = $model -> getSchema() -> getTable();
				$destination = $this -> getFilePath();


				$dir = dirname($destination);
				if(!file_exists($dir))
					mkdir($dir,0777,true);

				$move = file_put_contents($destination,$this -> content);


				if(!$move){
					throw new \Exception("File not saved");
				}
			}
		];
	}

	public function getFilePath(){
		return $this -> getDirBase().$this -> getDirObject().$this -> getDirModel($this -> getModel()).$this -> getValue();
	}

	public function getFullPath(){
		return $this -> getDirBase().$this -> getDirObject().$this -> getDirModel($this -> getModel()).$this -> getValue();
	}
}

?>