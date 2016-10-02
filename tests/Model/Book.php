<?php

namespace CoreWine\DataBase\Test\Model;

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
    public static function fields($schema){

    
        $schema -> id();
        
        $schema -> string('title')
                -> maxLength(128)
                -> minLength(3)
                -> required();

        $schema -> toOne(Author::class,'author');

        $schema -> toOne(Isbn::class,'isbn','isbn_code','code') -> required();

        $schema -> toMany(OrderBook::class,'booksorders','book_id')
                -> to('orders','order');

    }
}

?>