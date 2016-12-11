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

		$content = @file_get_contents($url);

		if($content === FALSE){
			throw new \Exception("Failed to retrieve [Model\Field\File]: ".$url);
		}

		return $this -> setByContent($content,$filename);
		
	}

	public function link($url){
		if(file_exists($url)){
			$basename = basename($url);
			$this -> setValue($basename);
		}	
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
				$destination = $this -> file($this -> getValue());



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

	public function makeThumb($source,$destination,$thumb_width,$thumb_height){

	    $arr_image_details = getimagesize($source);

	    $original_width = $arr_image_details[0];
	    $original_height = $arr_image_details[1];

	    if($original_width == 0 || $original_height == 0){
	    	return $source;
	    }

	    if($original_width > $original_height){
	        $new_width = $thumb_width;
	        $new_height = intval($original_height * $new_width / $original_width);
	    }else{
	        $new_height = $thumb_height;
	        $new_width = intval($original_width * $new_height / $original_height);
	    }

	    $dest_x = intval(($thumb_width - $new_width) / 2);
	    $dest_y = intval(($thumb_height - $new_height) / 2);

	    if($arr_image_details[2] == IMAGETYPE_GIF){
	        $imgt = "ImageGIF";
	        $imgcreatefrom = "ImageCreateFromGIF";
	    }

	    if($arr_image_details[2] == IMAGETYPE_JPEG){
	        $imgt = "ImageJPEG";
	        $imgcreatefrom = "ImageCreateFromJPEG";
	    }

	    if($arr_image_details[2] == IMAGETYPE_PNG){
	        $imgt = "ImagePNG";
	        $imgcreatefrom = "ImageCreateFromPNG";
	    }

	    if($imgt){
	        $old_image = $imgcreatefrom($source);
	        $new_image = imagecreatetruecolor($thumb_width, $thumb_height);
	        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);

	        $imgt($new_image, $destination);

	        return true;
	    }

	    throw new \Exception("Error during creation of thumbnail");
	}

	public function removeExtension($filename){
		return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
	}

	public function original(){
		return $this -> web($this -> getValue());
	}

	public function thumb($thumb_width,$thumb_height){

		$orig = $this -> file();

		if(!file_exists($orig))
			return $this -> getValue();

		$file = $this -> removeExtension($orig);
		$thumb = $file."_{$thumb_width}x{$thumb_height}.jpg";



		if(!file_exists($thumb))
			$this -> makeThumb($orig,$thumb,$thumb_width,$thumb_height);


		$web = $this -> removeExtension($this -> web());
		$web = $web."_{$thumb_width}x{$thumb_height}.jpg";

		echo $web;
	}

	public function web($file = null){
		return $this -> getSchema() -> getPathWeb().$this -> getSchema() -> callFilesystem($this);
	}

	public function file($file = null){
		return $this -> getSchema() -> getPathFile().$this -> getSchema() -> callFilesystem($this);
	}

	public function getValueToArray(){
		$r = [];

		$r['original'] = $this -> original();

		return $r;
	}
}

?>