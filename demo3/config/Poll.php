<?php
class Poll{
    private $host  = '127.0.0.1';
    private $user  = 'poll3';
    private $password   = '25152515?';
    private $database  = 'poll3';            
    private $dbConnect = false;
    private $pollTable = 'poll';
	public function __construct(){
		if(!$this->dbConnect){
		$conn = new mysqli($this->host, $this->user, $this->password, $this->database);
		if($conn->connect_error){
		die("Error failed to connect to MySQL: " . $conn->connect_error);
		}else{
		$this->dbConnect = $conn;
		}
		}
		}
		private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
		die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
		$data[]=$row;
		}
		return $data;
		}
		public function getPoll(){
		$sqlQuery = 'SELECT pollid, question, options, votes, voters FROM '.$this->pollTable;
		return $this->getData($sqlQuery);
		}
		public function getVotes($pollid){
		$sqlQuery = 'SELECT votes, voters FROM '.$this->pollTable.' where pollid = '.$pollid;
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		return mysqli_fetch_array($result, MYSQL_ASSOC);
		}
		}
		?>