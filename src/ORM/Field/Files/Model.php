<?php

namespace CoreWine\DataBase\ORM\Field\Files;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * List of all files
	 *
	 * @var Array
	 */
	public $files = [];

	public $value_out = [];

	/**
	 * Retrieve a value raw given a value out
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueRawByValueOut($value){
		
		return json_encode($value);
	}

	/**
	 * Retrieve a value out given a value raw
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueOutByValueRaw($value){

		return json_decode($value,true);
	}

	/**
	 * Get content by filename
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public function getContentByFilename($filename){
		return @file_get_contents($filename);
	}

	/**
	 * Remove all
	 *
	 * @return void
	 */
	public function clear(){
		foreach((array)$this -> all() as $index => $filename){
			unlink($this -> file($filename));
		}

		$this -> setValue([]);
	}

	/**
	 * Get all
	 */
	public function all(){
		return $this -> getValue();
	}

	/**
	 * Add a value using direct link
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public function link($url){
		if(file_exists($url)){
			$basename = basename($url);
			$this -> setValue($basename);
		}	
	}


	/**
	 * Add a file given url
	 *
	 * @param string $url
	 * @param string $filename
	 * 
	 * @return void
	 */
	public function addByUrl($url,$filename = null){
		
		if($filename == null)
			$filename = basename($url);

		$content = $this -> getContentByFilename($url);

		if($content === FALSE)
			throw new \Exception("Failed to retrieve [Model\Field\File]: ".$url);
		

		return $this -> addByContent($content,$filename);
		
	}

	/**
	 * Parse filename in order to save in filesystem without problems
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public function getFileNameParsed($filename){

		return preg_replace('/\?.*/', '', $filename);

	}

	/**
	 * Add a file using content
	 *
	 * @param string $content
	 * @param string $filename
	 *
	 * @return void
	 */
	public function addByContent($content,$filename){

		$filename = $this -> getFileNameParsed($filename);

		# Check for resizing...
		# Check for extension...
		$this -> files[$filename] = $content;

		$this -> addValue($filename);
	}

	public function addValue($filename){

		if(!is_array($this -> getValue())){
			$this -> setValue([]);
		}

		$this -> setValue(array_merge($this -> getValue(),[$filename]));

		print_r($this -> getValue());
	}

	public function getDirObject(){
		return $this -> getSchema() -> getDirObject();
	}

	public function getDirModel($model){
		return $this -> getSchema() -> getDirModel($model);
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

	public function original($index){
		return $this -> web($index);
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

		return $web;
	}

	public function web($index){
		return $this -> getSchema() -> getPathWeb().$this -> getSchema() -> callFilesystem($this,$index);
	}

	/**
	 * Get path file
	 */
	public function file($index){
		return $this -> getSchema() -> getPathFile().$this -> getSchema() -> callFilesystem($this,$index);
	}

	/**
	 * Get value of this model when it's called toArray
	 *
	 * @return Array
	 */
	public function getValueToArray(){
		$r = [];

		$r['original'] = $this -> getValueRaw();

		return $r;
	}


	/**
	 * Define events callback
	 *
	 * @return array
	 */
	public function events(){

		return [
			'saved' => function($model){

				if(!$this -> files)
					return;
			

				$table = $model -> getSchema() -> getTable();


				foreach($this -> files as $filename => $content){

					$destination = $this -> file(basename($filename));

					$dir = dirname($destination);

					if(!file_exists($dir))
						mkdir($dir,0777,true);


					$move = file_put_contents($destination,$content);

					if(!$move){
						throw new \Exception("An error has occurred during saving: $destination");
					}
				}



			}
		];
	}
}
?>