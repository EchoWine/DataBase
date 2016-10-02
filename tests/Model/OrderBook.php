<?php

namespace CoreWine\DataBase\Test\Model;

use CoreWine\DataBase\ORM\Model;

class OrderBook extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'orders_books';

    /**
     * Set schema fields
     *
     * @param Schema $schema
     */
    public static function fields($schema){

        $schema -> id();
        
        $schema -> toOne(Order::class,'order') -> required();

        $schema -> toOne(Book::class,'book') -> required();

        
    }
}

?>