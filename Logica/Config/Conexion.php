<?php

class DataBase {
    protected static $conex;
    public static $host = 'localhost';
    public static $username = 'root';
    public static $password = '';
    public static $database = 'pruebaintelcost';


	protected static function connect() {
		try{
		
			self::$conex = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$database, self::$username, self::$password);
		
		}catch(PDOException $e){

			printf($e->getMessage());
			die();
		
		}
		
		self::$conex->query("SET NAMES 'utf8'");
		
		return self::$conex;
	}

	protected static function die(){
		self::$conex = null;
	}
}