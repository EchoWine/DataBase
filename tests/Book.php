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

        # ID
        $schema -> id();

        # Door
        $schema -> string('title')
                -> maxLength(128)
                -> minLength(3)
                -> required();

    }
}

?>