<?php
class DataBase {
	public $pdo;
	private $stmt;
	// Construct and connect to database
	// database object will be $this->pdo
	public function __construct() {
		date_default_timezone_set('Asia/Tehran');
		set_exception_handler(function($e) {
			error_log($e->getMessage());
			exit($e->getMessage());
		});
		$dsn = "mysql:host=localhost;dbname=".DB_NAME.";charset=UTF8";
		$options = [
			PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
		];
		$this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
		return $this->pdo;
	}

	// Prepare statement with query
	public function select($sql,$params=NULL){
		$this->stmt = $this->pdo->prepare($sql);
		$this->stmt->execute($params);
		return $this->stmt->fetchAll();
	}
	public function selectOne($sql,$params=NULL){
		$this->stmt = $this->pdo->prepare($sql);
		$this->stmt->execute($params);
		return $this->stmt->fetch();
	}
	public function ifExists($sql,$params=NULL){
		$this->stmt = $this->pdo->prepare($sql);
		if($params)
			$this->stmt->execute($params);
		else
			$this->stmt->execute();
		return $this->rowCount();
	}

	// Get row count
	public function rowCount(){
		return $this->stmt->rowCount();
	}
	// Get last insert Id
	public function lastInsertId(){
		return $this->pdo->lastInsertId();
	}
	// Build query for insert
	private function queryBuilder($tblName, $insertItems) {
		$c; $v;
		foreach ($insertItems as $key => $value) {
			$c .= '`'.$key.'`,';
			$v .= ':'.$key.',';
		}
		$c = rtrim($c,',');
		$v = rtrim($v,',');
		$finalQuery = "INSERT INTO `{$tblName}` ({$c}) VALUES ({$v})";
		return $finalQuery;
	}

	// Insert, just give table name as first arg
	// and an associative array for insert items
	// please note that you have to pass insert items like this:
	// 
	// $insertt = [
    //     'username' => $_POST['username'],
    //     'email' => $_POST['email'],
    //     'password' => $_POST['password'],
    //     'name' => $_POST['name']
	// ];
	// 
	// so in a nutshell, give column name as array items KEY
	public function insert($tblName, $insertItems) {
		$this->stmt = $this->pdo->prepare(
			$this->queryBuilder($tblName, $insertItems)
		);
		$this->stmt->execute($insertItems);
		return $this->lastInsertId();
	}
}
