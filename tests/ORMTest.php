<?php

use PHPUnit\Framework\TestCase;

use CoreWine\DataBase\DB;
use CoreWine\DataBase\ORM\SchemaBuilder;
use CoreWine\DataBase\Test\Model\Book;
use CoreWine\DataBase\Test\Model\Author;
use CoreWine\DataBase\Test\Model\Isbn;

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

    }

    public function testBasicORM(){

        SchemaBuilder::setFields([
            'toOne' => CoreWine\DataBase\ORM\Field\Relations\ToOne\Schema::class,
            'toMany' => CoreWine\DataBase\ORM\Field\Relations\ToMany\Schema::class,
            'string' => CoreWine\DataBase\ORM\Field\String\Schema::class,
            'id' => CoreWine\DataBase\ORM\Field\Identifier\Schema::class,
            'timestamp' => CoreWine\DataBase\ORM\Field\Timestamp\Schema::class,
            'text' => CoreWine\DataBase\ORM\Field\Text\Schema::class,
            'email' => CoreWine\DataBase\ORM\Field\Email\Schema::class,
        ]);

        Author::truncate();
        Book::truncate();
        Isbn::truncate();

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

    }
}