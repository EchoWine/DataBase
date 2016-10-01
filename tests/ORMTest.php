<?php

use PHPUnit\Framework\TestCase;

use CoreWine\DataBase\DB;
use CoreWine\DataBase\ORM\SchemaBuilder;
use CoreWine\DataBase\Test\Model\Book;
use CoreWine\DataBase\Test\Model\Author;
use CoreWine\DataBase\Test\Model\Isbn;
use CoreWine\DataBase\Test\Model\Order;


class ORMTest extends TestCase{
   
    public function testConnection(){

    	DB::connect([
    		'driver' => 'mysql',
			'hostname' => '127.0.0.1',
			'database' => 't3st_db',
			'username' => 'root',
			'password' => '',
			'charset'  => 'utf8',
			'restore' => 5,
			'alter_schema' => true,
    	]);

        SchemaBuilder::setFields([
            'toOne' => CoreWine\DataBase\ORM\Field\Relations\ToOne\Schema::class,
            'toMany' => CoreWine\DataBase\ORM\Field\Relations\ToMany\Schema::class,
            'throughMany' => CoreWine\DataBase\ORM\Field\Relations\ThroughMany\Schema::class,
            'string' => CoreWine\DataBase\ORM\Field\String\Schema::class,
            'id' => CoreWine\DataBase\ORM\Field\Identifier\Schema::class,
            'timestamp' => CoreWine\DataBase\ORM\Field\Timestamp\Schema::class,
            'text' => CoreWine\DataBase\ORM\Field\Text\Schema::class,
            'email' => CoreWine\DataBase\ORM\Field\Email\Schema::class,
        ]);

        Author::truncate();
        Book::truncate();
        Isbn::truncate();
    }

    public function testBasicRelations(){

        $book = new Book();
        $book -> title = "The Hitchhiker's Guide to the Galaxy";
        $book -> save();

        $book -> delete();

        $author = new Author();
        $author -> name = 'Ban';
        $author -> save();

        $isbn = new Isbn();
        $isbn -> code = '978-3-16-148410-0';
        $isbn -> save();

        $book -> author = $author;
        $book -> isbn = $isbn;

        $book -> save();
        

        $book = Book::first();
        
        $this -> assertEquals($book -> author -> name,"Ban");
        $this -> assertEquals($book -> isbn -> code,"978-3-16-148410-0");


        $book -> author = $author;
        $book -> author -> name = 'Robinson';
        $this -> assertEquals($book -> author -> name,"Robinson");
    }

    public function testAdvancedRelations(){
        
        $author = Author::first();
        $book = new Book;
        $book -> title = "Eragon";
        $book -> isbn = new Isbn();
        $book -> isbn -> code = "978-4-16-148410-0";
        $book -> isbn -> save();


        $book2 = Book::first();
        $author -> books -> remove($book2);
        $author -> books -> add($book);
        $author -> books[] = $book;
        $author -> books -> save();

        //print_r(\CoreWine\DataBase\DB::log(true));

        //$author -> books -> sync([$book]);

        return;

        $order = new Order();
        $order -> transaction = '1234567890';
        $order -> save();


        $order -> books -> add($book);

        $order -> books -> save();


        $book -> orders -> remove($order);
        $book -> orders -> save();

        
    }
}