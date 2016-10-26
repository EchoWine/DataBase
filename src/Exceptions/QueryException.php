<?php

namespace CoreWine\DataBase\Exceptions;

use CoreWine\Component\Exception;

class QueryException extends Exception{

	public function __construct($message,$query,$params = []){


		if(!empty($params)){

			$params = array_reverse($params);
			$keys = array_keys($params);
			$values = array_values($params);

			foreach($values as &$e)
				if(is_string($e))$e = "'{$e}'";

			$sent = str_replace($keys,$values,$query);
		}else{

			$sent = $query;
		}	

		$this -> info['raw'] = $query;
		$this -> info['params'] = $params;
		$this -> info['sent'] = $sent;

		parent::__construct($message);
	}
}

?>