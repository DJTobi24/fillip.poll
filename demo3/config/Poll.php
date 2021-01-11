<?php
class Poll{
    private $host  = 'localhost';
    private $user  = 'root';
    private $password   = '';
    private $database  = 'demos';            
    private $dbConnect = false;
    private $pollTable = 'poll';
    public function __construct(){
        if(!$this->dbConnect){ 
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
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
        return  $this->getData($sqlQuery);
	}
	public function getVotes($pollid){
		$sqlQuery = 'SELECT votes, voters FROM '.$this->pollTable.' where pollid = '.$pollid;
		$result = mysqli_query($this->dbConnect, $sqlQuery);
        return  mysqli_fetch_array($result, MYSQL_ASSOC);
	}
	public function updateVote($pollVoteData) {	
		if(!isset($pollVoteData['pollid']) || isset($_COOKIE[$pollVoteData['pollid']])) {
           return false;
        }
		$pollVoteDetails = $this->getVotes($pollVoteData['pollid']);
		$votes = explode("||||", $pollVoteDetails['votes']);
		$votes[$pollVoteData['pollOptions']] += 1;
		implode("||||",$votes);
		$pollVoteDetails['voters'] += 1;
		$sqlQuery = "UPDATE ".$this->pollTable." set votes = '".implode("||||",$votes)."' , voters = '".$pollVoteDetails['voters']."' where pollid = '".$pollVoteData['pollid']."'";
		mysqli_query($this->dbConnect, $sqlQuery);
		return true;
	}
}
?>