<?php

namespace CoreWine\DataBase\ORM\Field\CreatedAt;

use CoreWine\DataBase\ORM\Field\DateTime\Schema as FieldSchema;

class Schema extends FieldSchema{
		
	/**
	 * Construct
	 */
	public function __construct($name = null){
		if($name == null){
			$name = 'created_at';
		}

		parent::__construct($name);
	}

}

?>