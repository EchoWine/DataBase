<?php

use PHPUnit\Framework\TestCase;

use CoreWine\DataBase\DB;
use CoreWine\DataBase\Test\Book;
use CoreWine\DataBase\ORM\SchemaBuilder;

class BasicTest extends TestCase{
   
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

    public function testSimpleQuery(){
    	
    	# --------------------------------------------------------------------------

    	# Create table and truncate
    	DB::query("CREATE TABLE IF NOT EXISTS users (
    		id INT(11)  AUTO_INCREMENT,
    		username VARCHAR(55),
    		password text,
    		PRIMARY KEY (id)
    	)");

    	DB::query("TRUNCATE users");
    	# --------------------------------------------------------------------------
    	
    	# Insert some data
    	DB::execute("INSERT INTO users (username,password) VALUES (:u1,:p1),('guest','guest')",[':u1' => 'admin',':p1' => 'admin']);
    	
    	# Get "first" of last insert id
    	$this -> assertEquals(1,DB::getInsertID());

    	# --------------------------------------------------------------------------

    	# Check results
    	$results = DB::fetch("SELECT * FROM users");
    	$this -> assertEquals($results[0],['id' => 1,0 => 1,'username' => 'admin',1 => 'admin','password' => 'admin',2 => 'admin']);

    	# --------------------------------------------------------------------------

    	# Check count
    	$this -> assertEquals(DB::count(DB::query("SELECT * FROM users")),2);

    	# --------------------------------------------------------------------------

    	DB::query("TRUNCATE users");

    	# --------------------------------------------------------------------------

    	
    }

    public function testTransaction(){
    	
    	# --------------------------------------------------------------------------

    	# Transaction:success
    	DB::beginTransaction();
    	DB::execute("INSERT INTO users (username,password) VALUES (:u1,:p1)",[':u1' => 'admin',':p1' => 'admin']);
    	DB::commit();

    	# --------------------------------------------------------------------------

    	# Transaction:error
    	DB::beginTransaction();
    	DB::execute("INSERT INTO users (username,password) VALUES (:u1,:p1)",[':u1' => 'admin',':p1' => 'admin']);
    	DB::rollback();

    	# Check
    	$this -> assertEquals(DB::count(DB::query("SELECT * FROM users")),1);

    	# --------------------------------------------------------------------------

    	# Transaction in a closuure
    	DB::transaction(function(){
    		DB::query("DELETE FROM user WHERE id = 1");
    		throw new \Exception("Some goes wrong, must rollback");
		});

		# Check
    	$this -> assertEquals(DB::count(DB::query("SELECT * FROM users")),1);

    	# --------------------------------------------------------------------------

    	DB::query("TRUNCATE users");
    }

    public function testRestore(){
    	
    	# --------------------------------------------------------------------------

    	# Insert some data
    	DB::query("INSERT INTO users (username,password) VALUES ('admin','admin')");

    	# --------------------------------------------------------------------------

    	# Save the table
    	DB::save('users');

    	# Perform some query
    	DB::query("TRUNCATE users");

    	# Now users is recovered!
    	DB::undo();

    	# Check
    	$this -> assertEquals(DB::count(DB::query("SELECT * FROM users")),1);

    	# --------------------------------------------------------------------------

    	# Save the table
    	DB::save('users');

    	# Perform some query
    	DB::query("TRUNCATE users");

    	# Now users is empty!
    	DB::confirm();

    	# Check
    	$this -> assertEquals(DB::count(DB::query("SELECT * FROM users")),0);

    	# After several rows of code...
    	# Restore the LAST SAVE POINT
    	DB::restore();

    	# Check
    	$this -> assertEquals(DB::count(DB::query("SELECT * FROM users")),1);

    	# --------------------------------------------------------------------------

    	# But WAIT! You can undo the previous restore!
    	DB::restore();

    	# Check
    	$this -> assertEquals(DB::count(DB::query("SELECT * FROM users")),0);
    	
    	# --------------------------------------------------------------------------

    	# Oh yeah, restore the restore of restore point! (undo x3)
    	DB::restore();

    	# Check
    	$this -> assertEquals(DB::count(DB::query("SELECT * FROM users")),1);

    	# --------------------------------------------------------------------------

    	DB::query("TRUNCATE users");

    	# --------------------------------------------------------------------------
    }

    public function testQueryBuilder(){

        DB::schema('tab1',function($tab){
            $tab -> id();
            $tab -> string('Name') -> unique();
            $tab -> string('foo') -> null();
            $tab -> string('foDo') -> default('abcde') -> null();
            $tab -> int('fo1os');
        });

        DB::startLog();
        DB::schema('tab2') -> id() -> alter();
        DB::schema('tab2') -> bigint('tab1_id') -> foreign('tab1','id') -> alter();

        DB::schema('tab3') -> id() -> alter();
        DB::schema('tab3') -> bigint('tab1_id') -> foreign('tab1','id') -> alter();
        DB::schema('tab3') -> string('username') -> unique() -> alter();

        DB::schema('tab3_tab2') -> bigint('tab3_id') -> foreign('tab3','id') -> alter();
        DB::schema('tab3_tab2') -> bigint('tab2_id') -> foreign('tab2','id') -> alter();
        DB::schema('tab3_tab2') -> bigint('taxi') -> alter();

        $tab1_id = DB::table('tab1') -> insert([
            ['name' => md5(microtime()),'foo' => null],
            ['name' => md5(microtime()),'foo' => null],
            ['name' => md5(microtime()),'foo' => '123']
        ]);
        
        $tab2_id = DB::table('tab2') -> insert(['tab1_id' => $tab1_id[0]]);
        $tab3_id = DB::table('tab3') -> insert(['tab1_id' => $tab1_id[1],'username' => md5(microtime())]);
        DB::table('tab3_tab2') -> insert(['tab2_id' => $tab2_id[0],'tab3_id' => $tab3_id[0],'taxi' => 5]);


        DB::table('tab1') -> insert(['name' => md5(microtime())]);


        /* --------------------------------------

                JOIN

        --------------------------------------- */

        DB::table('tab2') -> join('tab3_tab2','tab3_tab2.tab2_id','=','tab2.id') -> join('tab3','tab3_tab2.tab3_id','=','tab3.id') -> get();


        DB::table('tab3_tab2') -> join(['tab3','tab2']) -> get();


        DB::table('tab2 as ttt') -> join('tab3_tab2') -> join('tab3') -> get();


        DB::table('tab2 as tb2')
        -> join('tab3_tab2 as tb32',function($q){

            $q = $q -> where('tb32.taxi','=',5);
            return $q;

        }) -> join('tab3') -> get();

        
        DB::table('tab2')
        -> join('tab3_tab2',function($q){
            $q = $q -> on('tab3_tab2.tab2_id','=','tab2.id');
            $q = $q -> where('tab3_tab2.taxi','=',5);
            return $q;

        }) -> join('tab3') -> get();
        
        DB::table('tab3_tab2') -> join(['tab3' => function($q){
            $q = $q -> where('tab3_tab2.taxi','=',5);
            return $q;
        },'tab2']) -> get();


        DB::table('tab2')
        -> join('tab3_tab2',function($q){
            $q = $q -> on(function($q){
                return $q -> orOn('tab3_tab2.tab2_id','=','tab2.id') -> orOn('tab3_tab2.tab2_id','=','tab2.id');
            });
            $q = $q -> on(function($q){
                return $q -> orOn('tab3_tab2.tab2_id','=','tab2.id') -> orOn('tab3_tab2.tab2_id','=','tab2.id');
            });
            $q = $q -> where('tab3_tab2.taxi','=',5);
            return $q;

        }) -> join('tab3') -> get();
        
        DB::table('tab2')
        -> crossJoin('tab3_tab2')
        -> join('tab3')
        -> get();
        
     
        /*
        DB::table('tab1') -> insert(function(){
            return DB::table('tab1') -> select('name');
        });
        */

        /*
        DB::schema('tab3') -> dropColumn('username');
        DB::schema('tab3') -> drop();
        */

        DB::table(function(){
            return DB::table(function(){
                return DB::table('tab1');
            },'w43');
        })
        -> union(DB::table('tab1'))
        -> get();

        DB::table('tab1 as r')
        -> orWhere(function($q){
            $q = $q -> orWhere('foo','123');
            $q = $q -> orWhereIn('foo',['123']);
            $q = $q -> orWhereLike('foo','%123%');
            $q = $q -> orWhereNull('foo');
            $q = $q -> orWhereNotNull('foo');
            $q = $q -> orWhereNotBetween('foo',[1,10]);
            $q = $q -> orWhereBetween('foo',[1,10]);
            return $q;
        })
        -> orWhere(function($q){
            $q = $q -> where('foo','123');
            $q = $q -> whereIn('foo',['123']);
            $q = $q -> whereLike('foo','%123%');
            $q = $q -> whereNull('foo');
            $q = $q -> whereNotNull('foo');
            $q = $q -> whereNotBetween('foo',[1,10]);
            $q = $q -> whereBetween('foo',[1,10]);
            return $q;
        })
        -> orHaving(function($q){
            $q = $q -> orHaving('foo','123');
            $q = $q -> orHavingIn('foo',['123']);
            $q = $q -> orHavingLike('foo','%123%');
            $q = $q -> orHavingNull('foo');
            $q = $q -> orHavingNotNull('foo');
            $q = $q -> orHavingNotBetween('foo',[1,10]);
            $q = $q -> orHavingBetween('foo',[1,10]);
            return $q;
        })
        -> orHaving(function($q){
            $q = $q -> having('foo','123');
            $q = $q -> havingIn('foo',['123']);
            $q = $q -> havingLike('foo','%123%');
            $q = $q -> havingNull('foo');
            $q = $q -> havingNotNull('foo');
            $q = $q -> havingNotBetween('foo',[1,10]);
            $q = $q -> havingBetween('foo',[1,10]);
            return $q;
        })
        -> get();

        DB::table('tab1') -> count();
        DB::table('tab1') -> max('id');
        DB::table('tab1') -> min('id');
        DB::table('tab1') -> avg('id');

        DB::table('tab1') -> where('id',1) -> update('foo','bla');
        DB::table('tab1') -> where('id',1) -> update(['foo' => 'bla']);

        DB::table('tab1') -> where('id',1) -> update(
        [
            ['id','foo']

        ],
        [
            [
                0 => 'bla',
                1 => 'cia'
            ]
        ]
        );

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


        $book = new Book();
        $book -> title = "The Hitchhiker's Guide to the Galaxy";
        $book -> save();
    }
}