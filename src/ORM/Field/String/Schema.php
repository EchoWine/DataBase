<?php

namespace CoreWine\DataBase\ORM\Field\String;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{

	/**
	 * Max length
	 *
	 * @var bool
	 */
	public $max_length = 255;

	/**
	 * Min length
	 *
	 * @var bool
	 */
	public $min_length = 0;

	/**
	 * Regex of field
	 */
	public $regex = "/^(.){0,255}$/iU";
}

?>