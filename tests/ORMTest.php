<?php

use PHPUnit\Framework\TestCase;

use CoreWine\DataBase\DB;
use CoreWine\DataBase\ORM\SchemaBuilder;
use CoreWine\DataBase\Test\Model\Book;
use CoreWine\DataBase\Test\Model\Author;
use CoreWine\DataBase\Test\Model\Isbn;
use CoreWine\DataBase\Test\Model\Order;
use CoreWine\DataBase\Test\Model\OrderBook;


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
            'belongsToOne' => CoreWine\DataBase\ORM\Field\Relations\BelongsToOne\Schema::class,
            'throughMany' => CoreWine\DataBase\ORM\Field\Relations\ThroughMany\Schema::class,
            'string' => CoreWine\DataBase\ORM\Field\String\Schema::class,
            'id' => CoreWine\DataBase\ORM\Field\Identifier\Schema::class,
            'timestamp' => CoreWine\DataBase\ORM\Field\Timestamp\Schema::class,
            'text' => CoreWine\DataBase\ORM\Field\Text\Schema::class,
            'email' => CoreWine\DataBase\ORM\Field\Email\Schema::class,
            'datetime' => CoreWine\DataBase\ORM\Field\DateTime\Schema::class,
            'updated_at' => CoreWine\DataBase\ORM\Field\UpdatedAt\Schema::class,
            'created_at' => CoreWine\DataBase\ORM\Field\CreatedAt\Schema::class,
        ]);

        Author::truncate();
        Book::truncate();
        Isbn::truncate();
        Order::truncate();
        OrderBook::truncate();
    }


    public function log(){
    	print_r(DB::log(true));
    }

    public function testFields(){

        $book = new Book();
        $book -> title = "The Hitchhiker's Guide to the Galaxy";

        $time = new \DateTime();


        $book -> save();
        $this -> assertEquals($book -> id,1);

        $book = Book::first();


        $this -> assertEquals($book -> created_at -> format('d-m-Y'),$time -> format('d-m-Y'));
        
        $book -> delete();

    }

    public function testBasicRelations(){

        $book = new Book();
        $book -> title = "The Holy Bible";
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

        $book -> isbn -> book -> title = 'Sponge Documentation';

        $this -> assertEquals($book -> author -> name,"Ban");
        $this -> assertEquals($book -> isbn -> code,"978-3-16-148410-0");

        $this -> assertEquals($book -> isbn -> book -> title,'Sponge Documentation');

        $isbn = $book -> isbn;
        $book -> isbn;

        $isbn -> book = new Book();
        $isbn -> book -> title = 'PHP Documentation';
        $isbn -> book -> save();


        $this -> assertEquals($book -> isbn,null);
        
        $isbn = Isbn::first();

        $this -> assertEquals($isbn -> book -> title,'PHP Documentation');


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



        $this -> assertEquals($author -> books -> count(),1);
        $author -> books -> remove($book2);
        $this -> assertEquals($author -> books -> count(),0);
        $author -> books -> add($book);

        $this -> assertEquals($author -> books -> count(),1);
        $author -> books[] = $book;

        $this -> assertEquals($author -> books -> count(),1);
        
        $author -> books -> save();



        //$author -> books -> sync([$book]);


        $order = new Order();
        $order -> transaction = '1234567890';
        $order -> save();
        $this -> assertEquals($order -> id,1);


        $ob = new OrderBook();
        $ob -> book = $book;
        $ob -> order = $order;
        $ob -> save();

        $order = Order::where('id',1) -> first();

        foreach($order -> books as $book){
        }



        $order -> books -> add($book2);
        

        $order -> books -> remove($book);
        $order -> books -> save();

        $book = Book::create([
            'title' => 'New new new'
        ]);

        $book -> orders -> add($order);
        $book -> orders -> save();

        /*
        foreach($book -> orders as $order){
            echo $order;
        }
        */


        //$order -> orders_books() -> where('book_id',1) -> get() -> retrieve('books',true);
       
    }
}