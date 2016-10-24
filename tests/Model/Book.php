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

        $schema -> toOne(Isbn::class,'isbn','isbn_code','code');

        $schema -> throughMany('orders',Order::class) -> resolver(OrderBook::class,'book','order');

        // $schema -> throughMany('orders',Order::class,'id','id');
        // $schema -> throughMany('orders',Order::class) -> inModel(OrderBook::class)
        // $schema -> throughMany('orders',Order::class) -> inTable('orders_books')
        // $this -> orders -> add(Order $order);
        // $this -> orders // Collection orders
        // $this -> orders -> get(0) -> pivot. OrderBoook
                
        $schema -> updated_at();
        $schema -> created_at();

    }
}

?>