<?php

use PHPUnit\Framework\TestCase;

use CoreWine\DataBase\DB;

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
}