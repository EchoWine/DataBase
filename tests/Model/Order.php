<?php

namespace CoreWine\DataBase\Test\Model;

use CoreWine\DataBase\ORM\Model;

class Order extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'orders';

    /**
     * Set schema fields
     *
     * @param Schema $schema
     */
    public static function fields($schema){

        $schema -> id();

        $schema -> string('transaction')
                -> required();


        $schema -> throughMany('books',Book::class) -> resolver(OrderBook::class,'order','book');
        
    }
}

?>