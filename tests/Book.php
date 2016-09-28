<?php

namespace CoreWine\DataBase\Test;

use CoreWine\DataBase\ORM\Model;

class Book extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'books';

    /**
     * Set schema fields
     *
     * @param Schema $schema
     */
    public static function setSchemaFields($schema){

    
        $schema -> id();
        
        $schema -> string('title')
                -> maxLength(128)
                -> minLength(3)
                -> required();

        $schema -> toOne(Author::class,'author');
        $schema -> toOne(Author::class,'author_second_by_id','author_second_id');
        $schema -> toOne(Author::class,'author_second_by_name','author_second_name','name');
    }
}

?>