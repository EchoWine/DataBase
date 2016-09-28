<?php

namespace CoreWine\DataBase\Test;

use CoreWine\DataBase\ORM\Model;

class Author extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'authors';

    /**
     * Set schema fields
     *
     * @param Schema $schema
     */
    public static function setSchemaFields($schema){

        # ID
        $schema -> id();

        # Door
        $schema -> string('name')
                -> required();

    }
}

?>