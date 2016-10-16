<?php

namespace CoreWine\DataBase\ORM\Field\UpdatedAt;

use CoreWine\DataBase\ORM\Field\Datetime\Schema as FieldSchema;

class Schema extends FieldSchema{
	
	/**
	 * Construct
	 */
	public function __construct($name = null){
		if($name == null){
			$name = 'updated_at';
		}

		parent::__construct($name);
	}

}

?>