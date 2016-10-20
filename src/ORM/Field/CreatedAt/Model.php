<?php

namespace CoreWine\DataBase\ORM\Field\CreatedAt;

use CoreWine\DataBase\ORM\Field\DateTime\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * Define events callback
	 *
	 *
	 * @return array
	 */
	public function events(){

		return [
			'new' => function($model){
				$this -> setValue(new \DateTime());
			}
		];
	}

}
?>