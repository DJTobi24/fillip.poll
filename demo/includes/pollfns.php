<?php
/*------------	this is the Basic poll fonctionalities script			---------------*/
@session_start();
$complete_path ="";

require_once('dbfns.php');
	global	$hostName ;	
	global	$userName ;
	global	$password ;
	global	$database ;
$connect=connect($hostName,$userName,$password,$database);
if(!$connect){echo'Connection failed ! , please try again later ';exit;}

$votedIp=false; 	// ip statue (voted or not)
//check out the poll language
$q="SELECT `value` from `params` where name='lang' limit 1";

$res=query($connect,$q);
if($res){
	$res_arr=retrieve_data($res);
	$language=$res_arr[0]['value'];
	switch($language){
		case'en':
		require_once('language/en.php');
		break;
		
		case'fr':
		require_once('language/fr.php');
		break;
		
		case'es':
		require_once('language/es.php');
		break;
		
		case'it':
		require_once('language/it.php');
		break;
		
		case'de':
		require_once('language/de.php');
		break;
	}
}else{close($connect);die('<span class="msgLayout errorMsg"> Unable to get the poll language !</span>');}

//include all the scripts/style sheets needed
function includeScripts($relative_path){
	global $complete_path;
	$complete_path = $relative_path;
	$_SESSION['complete_path'] = $complete_path;
	echo'
	<link rel="stylesheet" type="text/css" href="'.$complete_path.'css/style.css" />
	<link rel="stylesheet" type="text/css" href="'.$complete_path.'dyna.php" />                    
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="'.$complete_path.'js/cp.js" type="text/javascript"></script>
	<script src="'.$complete_path.'js/jquery.autocomplete.js" type="text/javascript"></script>
	<script src="'.$complete_path.'js/script.js" type="text/javascript"></script>
	';
}

// prepare the layout of the poll
function newPoll($ref){
	global $complete_path;
	echo'
	<div class="pollWrapper" id="wrap-'.$ref.'">
		<div class="pollData" id="d-wrap-'.$ref.'">
		</div>
		<div class="loader" id="l-wrap-'.$ref.'">
			<img src="'.$complete_path.'images/loader.gif"/> Loading...
		</div>	
	</div>';
}

// the showresults() shows the poll results 
function showresults($q_id,$votedOn,$votedIp=false,$ref,$preview=false){ //show the vote results

	global $connect,$strTotal,$strGoBack,$error22,$thx,$complete_path;
	$votesQ="SELECT `id`,`option`,`votesNumber` FROM `options` WHERE q_id='$q_id'"; //check if the admin has override the vote results
	$vr=query($connect,$votesQ);
	$vn=array();//vote numbers
	$vo=array();//vote options and their id
	$vnne=array(); //vote number not empty
	$no=array(); //votes Not overriden
	$optNumber=0; //options number
	$overrideCount=0; //how many options votes we will override
	$overrideTotal=0;
	if($vr){
		$votes_arr=retrieve_data($vr);
		$i=0;
		$j=0;
		$k=0;
		foreach($votes_arr as $arr){
			$vo[$i]['id']=$arr['id'];
			$vo[$i]['option']=$arr['option'];
			$vn[$i]=$arr['votesNumber'];
			if(!empty($arr['votesNumber']) || $arr['votesNumber']==='0' ){ //if the option's votes are overriden
				$vnne[$j]=$vo[$i]['id'];
				$vn[$j]=$arr['votesNumber'];
				$overrideTotal=$overrideTotal+$vn[$j];
				$j++;
			}else{
				$no[$k]=$vo[$i]['id'];
				$k++;
			}
			$optNumber=$i++;
		}
		$overrideCount=count($vnne); //how many overriden option
		$notOverrideCount=count($no);//how many no-overriden option
		$optNumber++;
	}
	$q1="SELECT COUNT(*) as totalvotes FROM `votes` WHERE o_id IN(SELECT `id` FROM `options` WHERE q_id='$q_id')"; //get the total votes
	$res1=query($connect,$q1);
	if($res1){
		$show='show'; // show the go back button or dont
		$res_arr=retrieve_data($res1);
		$total=$res_arr[0]['totalvotes']; //total votes
		$q2="SELECT options.id, options.option, COUNT(*) as votes FROM votes, options WHERE votes.o_id=options.id AND votes.o_id IN(SELECT id FROM options WHERE q_id='$q_id') GROUP BY votes.o_id order by options.id asc"; //get each option votes 
		$res2=query($connect,$q2);
		if($res2){
			if($optNumber==0){ // if we dont know how many options there is ,then we should check it out 
				$query="SELECT `id`,`option` FROM `options` WHERE q_id=$q_id order by id asc";
				$opt_result=query($connect,$query);
				if($opt_result){
				$nbr1=$opt_result->num_rows;
				if($nbr1>0){
						$opt_arr=retrieve_data($opt_result);
						$opt_nbr=count($opt_arr); //option number
					}
				}
			}else{ //if we have the options number
				$opt_arr=$vo; // then those are the options with their respective ID in an array
				$opt_nbr=$optNumber; //and this is the option numbers  
			}
			$res2_arr=retrieve_data($res2);
			$res_nbr2=count($res2_arr);
			if($res_nbr2!=0){
				$total=$overrideTotal; // the total votes is the ovveriden
				foreach($res2_arr as $d){ // here we will add the votes that were not overriden to the total votes
					for($l=0;$l<$notOverrideCount;$l++){ // we should loop through the options and see the not overriden option
						if($d['id']===$no[$l]){
							$total=$total+$d['votes']; //add the not overriden votes to the total
						}
					}
				}
				for($j=0;$j<$opt_nbr;$j++){	 //loop through all the options
					$dbOption=$opt_arr[$j]['option'];
					$dbOptionID=$opt_arr[$j]['id'];
					for($k=0;$k<$res_nbr2;$k++){ //loop through the options that has votes
						$resOption=$res2_arr[$k]['option'];
						$resOptionID=$res2_arr[$k]['id'];
						$optVotes=$res2_arr[$k]['votes'];
						if($dbOptionID==$resOptionID){ // if the option in the database is the same with the one which we already calculated their votes
							for($l=0;$l<$overrideCount;$l++){ // we should loop through the options and see if their votes should be overriden 
								if($resOptionID===$vnne[$l]){//if so we should replace the votes number
									$optVotes=$vn[$l]; //the new vote number
								}
							}
							$percent=round(($optVotes*100)/$total); //the precentage
							echo '<div class="option" ><p><b>'.$dbOption.'</b> <br /><em  class="pollStats">('.$percent.'%, '.$optVotes.' vote';
							if($optVotes>1){echo"s";};
							echo ')</em></p>';
							echo '<div class="bar ';
							if ($votedOn!="skip") {
								foreach($votedOn as $o_id){ //check on which option we have voted and show that this was their vote 
									if($o_id!='skip'){
										if($o_id==$opt_arr[$j]['id']){echo 'yourvote';$show='dont';}
									}	
								}
							}
							
							echo '" style="width: '.$percent.'%; " ></div></div>';
							break;
						}
						if($k==$res_nbr2-1){
							echo '<div class="option" ><p><b>'.$dbOption.'</b> <br /><em  class="pollStats">(0%, 0 vote)</em></p><div class="bar " style="width: 0%; " ></div></div>';
							}
						}
				}
			}else{
					echo $error22;
				 }
		}
	}
	echo '<br /><span class="totalVotes">'.$strTotal.': '.$total.'</span>'; 
	if($preview=='1'){
			if(!isset($_COOKIE["poll".$q_id])){
				if(!$votedIp && ($show=='show')){
					echo '<a href="#" id="vote-'.$ref.'" class="float goBack"><img src="'.$complete_path.'images/back.png" alt="'.$strGoBack.'"/> '.$strGoBack.'</a><div style="clear:both;"></div>';
				}
			}
		}
	if(!empty($votedOn) && $votedOn!='skip'){ //if we have just voted then show thank you message
		echo'<br /><br /><span class="msgLayout doneMsg" id="thxMsg-'.$ref.'"> '.$thx.' </span>';
	}
}

function get_tag($tag,$xml){	// this is needed to get the geocode of the user
	preg_match_all('/<'.$tag.'>(.*)<\/'.$tag.'>$/imU',$xml,$match);
	return $match[1];
}

function getUserLocation(){
	$ip=$_SERVER['REMOTE_ADDR'];
	$countryName=null;
	// Making an API call to Hostip: (getting the geodata of the user)	
	if($xml=@file_get_contents('http://api.hostip.info/?ip='.$ip)){
		$city = get_tag('gml:name',$xml);
		$city = $city[1];
		
		$countryName = get_tag('countryName',$xml);
		$countryName = $countryName[0];
		
		$countryAbbrev = get_tag('countryAbbrev',$xml);
		$countryAbbrev = $countryAbbrev[0];
	
		$countryName = str_replace('(Unknown Country?)','UNKNOWN',$countryName);
	}
	// In case the Hostip API fails: (or the server don't support file_get_contents() function !)
	if (!$countryName){
		$countryName='UNKNOWN';
		$countryAbbrev='XX';
		$city='(Unknown City?)';
	}
	return array($countryName,$countryAbbrev,$city);
}

//the insertVote() is the function which allow the insertion of a user's vote
//the arguments to be passed in are (question ID,option ID,Question id(from form when it's not available from DB),the reference of the poll area)
function insertVote($q_id,$votedOn,$votesArr,$ques_id,$ref){
	global $connect,$error1;
	$ref_expl=explode('wrap-',$ref);
	$ref_str=cleanInput('s',$ref_expl[1],1);
	$query="SELECT `expire` FROM `areas` where name='{$ref_str}' limit 1";
	$result=query($connect,$query);
	$expire=0;
	if($result){	//if we get a result
		if($result->num_rows){ 
			$row=retrieve_data($result); //get the data 
			$expire=$row['0']['expire'];
		}	
	}
	$location=getUserLocation();
	$countryName=$location[0];
	$countryAbbrev=$location[1];
	$city=$location[2];
	$time=gmdate('Y-m-d H:'.strftime('%M').':s');
	$ip=$_SERVER['REMOTE_ADDR'];
	//Insert the vote
	$votesNbr=count($votedOn);
	if($votesNbr<2){ //if we will insert one single vote
		$query="INSERT INTO `votes`(q_id,o_id, voteDate, ip,country,countryCode,city) VALUES('{$q_id}','{$votedOn[0]}','{$time}','{$ip}','{$countryName}','{$countryAbbrev}','{$city}')";
		$res=query($connect,$query);
		if($res){
			//Vote added to database
			if ($expire >= 0) {
				setcookie("poll".$ques_id, 'yes', time()+60*$expire);
			}
			if(!empty($votesArr[0]) || $votesArr[0]=='0'){ //if this option's vote is overriden
				$q="UPDATE `options` SET votesNumber=votesNumber+1 where id='{$votedOn[0]}' "; //add 1 vote to the overriden value
				$res=query($connect,$q);
				if(!$res){
					echo $error1;
					exit;
				}
			}
			showresults(intval($ques_id),$votedOn,true,$ref);
			close($connect);exit;		
		}else{
			echo $error1;
			exit;
		 }
	}else if($votesNbr>1){ //if we will insert multiple votes
		$error=0;
		$query="INSERT INTO `votes`(q_id,o_id, voteDate, ip,country,countryCode,city) VALUES('{$q_id}',?,'{$time}','{$ip}','{$countryName}','{$countryAbbrev}','{$city}')";
		$q="UPDATE `options` SET votesNumber=votesNumber+1 where id=? "; //add 1 vote to the overriden value
		$stmt = $connect->stmt_init();
		$stmt->prepare($query);
		$stmt->bind_param('i', $optID);
		for($i=0;$i<$votesNbr;$i++){
			$optID=$votedOn[$i];
			$stmt->execute();
			if(!$stmt->affected_rows){
				$error=1;
				break;
			}
		}
		
		$stmt->prepare($q); //prepare the query to update the overriden votes
		$stmt->bind_param('i', $optID);
		for($i=0;$i<$votesNbr;$i++){
			if(!empty($votesArr[$i]) || $votesArr[$i]=='0'){ //if the votes are overriden
				$optID=$votedOn[$i];
				$stmt->execute();
				if(!$stmt->affected_rows){
					$error=1;
					break;
				}
			}
		}
		$stmt->close();
		if(!$error){
			//Vote added to database
			if ($expire >= 0) {
				setcookie("poll".$ques_id, 'yes', time()+60*$expire);		
			}
			showresults(intval($ques_id),$votedOn,true,$ref);
			exit;
		}else{
			echo $error1;
			exit;
		 }
	}
}

// the vote() is the function that tell if the voting is possible or not
//the arguments to be passed are (question ID, OPTION) 
function vote($ques_id,$votedOn,$ref){
	global $connect,$error2;
	//get the poll id
	$q_id=intval($ques_id);
	$votesNbr=count($votedOn);
	$votesArr=array();
	if($votesNbr>1){ //if we are voting on multiple options
		$query="SELECT `votesNumber` FROM `options` WHERE `q_id`='{$q_id}' AND `id`=? limit 1";
		$stmt = $connect->stmt_init();
		$stmt->prepare($query);
		$stmt->bind_param('i', $opt_id);
		$index=0;
		foreach($votedOn as $opt_id){
			//$option=urldecode($opt);
			$stmt->execute();
			$stmt->bind_result($votes);
			while($stmt->fetch()) {
				//$idArr[$index]=$id;
				$votesArr[$index]=$votes;
				$index++;
		   	}
		}
		$stmt->close();
		if(!empty($votedOn)){
		insertVote($q_id,$votedOn,$votesArr,$ques_id,$ref);	//insert the vote into the data base
		}else{
			// if the data couldn't be retrieved 
			echo $error2;
			close($connect);
			exit;
		 }
	}else{ //if we are voting on one single option
		$query="SELECT `votesNumber` FROM `options` WHERE `q_id`='{$q_id}' AND `id`='{$votedOn[0]}' limit 1 ";
		$res=query($connect,$query);
		if($res){
			$res_arr=retrieve_data($res);
			$votesArr[0]=$res_arr[0]['votesNumber'];
			insertVote($q_id,$votedOn,$votesArr,$ques_id,$ref);	//insert the vote into the data base
		}else{
			// if the data couldn't be retrieved 
			echo $error2;
			exit;
		}
	}
}

// update the polls (based on the expire and start date)
function pollCronJob(){
	global $connect;
	$query="SELECT `id`, `start_on`,`expire_on`,`shown` FROM `questions`";
	$result=query($connect,$query);
	if($result){
		if($result->num_rows > 0){
			$data=retrieve_data($result); //get the data 
			$now = gmdate('Y-m-d H:'.strftime('%M').':s');
			$expiredPolls=array();
			$shouldStartPolls=array();
			foreach ($data as $d) {
				$id=$d['id'];
				$start=$d['start_on'];
				$expire=$d['expire_on'];
				$shown=$d['shown'];
				if(($now< $start || $now>=$expire) && $shown=='y'){ // should be expired => hide it
					array_push($expiredPolls,$id);
				}
				if($now>=$start && $now<$expire && $shown=='n'){ // should have started => be shown
					array_push($shouldStartPolls,$id);
				}
			}

			if (count($expiredPolls)>0) {
				$q="UPDATE `questions` SET shown='n' where id=? ";
				$stmt = $connect->stmt_init();
				$stmt->prepare($q);
				$stmt->bind_param('i', $q_id);
				foreach ($expiredPolls as $q_id) {
					$stmt->execute();
				}
				$stmt->close();
			}

			if (count($shouldStartPolls)>0) {
				$q="UPDATE `questions` SET shown='y' where id=? ";
				$stmt = $connect->stmt_init();
				$stmt->prepare($q);
				$stmt->bind_param('i', $q_id);
				foreach ($shouldStartPolls as $q_id) {
					$stmt->execute();
				}
				$stmt->close();
			}
			return true;
		}else{
			return 'noq'; // there are no question no need to continue the rest of the queries
		}
	}else{
		return false;
	}
}

// showPoll() is the function that shows the poll (depending on if the user has voted or not) 
function showPoll($ref){
	global $connect,$votedIp,$noQuesMsg,$strLogin,$error3,$error4,$error5,$strVote,$strResult;
	global $strTotal,$strGoBack,$error22,$error19;
	$ref_expl=explode('wrap-',$ref);
	$ref_str=cleanInput('s',$ref_expl[1],1);
	$query="SELECT `id`,`expire`,`preview` FROM `areas` where name='{$ref_str}' limit 1";
	$result=query($connect,$query);
	$areaID=0;
	$preview=0;
	$expire=0;
	if($result){	//if we get a result
		if($result->num_rows){ 
			$row=retrieve_data($result); //get the data 
			$areaID=$row['0']['id'];
			$expire=$row['0']['expire'];
			$preview=$row['0']['preview'];
		}	
	}
	$cronJob=pollCronJob();
	if($cronJob==="noq"){
		die('<span class="msgLayout errorMsg">Sorry, there is no poll to be displayed here !</span>');
	}else if(!$cronJob){
		die('<span class="msgLayout errorMsg">We were unable to get you the poll data without errors !<br />Error code: #P01</span>');
	}

	$id=0;  // to get the id in the data array
	$ip=$_SERVER['REMOTE_ADDR'];
	// Get the question of the recent poll
	$query="SELECT `id`, `question`,`multiple_vote`,`exclusiveTo` FROM `questions` where area='{$areaID}' AND shown='y' ORDER BY `start_on` desc limit 1";
	$result=query($connect,$query);
	if($result){	//if we get a result
		if($result->num_rows){ 
			$row=retrieve_data($result); //get the data 
			$data=$row[$id]; //the data array
			$multiple=$data['multiple_vote'];
			if(!$multiple){//if the question dont support multiple answers
				$inpType='radio'; // 1 answer
			}else{
				$inpType='checkbox'; // multiple answers
			}
		}else{$row=false;}
	}
	if($row){
		//display question
		echo "<p class=\"pollques\" >".$data['question']."</p><br />";
		$q_id=$data['id'];
		$exclusive=$data['exclusiveTo'];
	}else{
			// if no question is available
			echo $noQuesMsg;
			$exclusive="xx";
			close($connect);
			exit;
		}
	$exclusive=countryCode2Name($exclusive);
	//checking if the ip has voted already !
	if(!isset($_COOKIE["poll".$q_id])){ //if didn't vote yet
		//see if the ip has voted on this poll
		$q="SELECT `ip`,`q_id`,`voteDate` from `votes` where ip='{$ip}' and q_id='{$q_id}' order by voteDate desc limit 1";
		$res=query($connect,$q);
		if($res){
			$nbr=$res->num_rows;
			if($nbr>0){
				$d=retrieve_data($res);
				$voteTime=strtotime($d[0]['voteDate']); //the timestamp of the vote time
				$now=strtotime(gmdate('Y-m-d H:'.strftime('%M').':s')); //now timestamp
				$vt=$voteTime+$expire*60; //when we can vote again time ( after expiration )
				if($now>=$vt){ //if the current time is bigger than the expiration date than we can vote again
					$votedIp=false;
				}else{
					$votedIp=true;
				}
			}else{$votedIp=false;}
		}else{
			echo $error3;
			close($connect);
			exit;
		 }
	}else if($_COOKIE["poll".$q_id]=='yes'){
		$votedIp=true;
	}
	if( isset($_POST["result"]) || isset($_COOKIE["poll".$q_id]) || $votedIp){ //if the user has voted already
		if(isset($_POST["result"])){
			if ($_POST["result"]==1) {
				showresults($q_id,"skip",$votedIp,$ref,'1'); //show the poll results
			}
		}else{
			//if already voted or asked for results
			showresults($q_id,"skip",$votedIp,$ref);
		 }
		exit;
	}else{ //if the user didn't vote
		switch($preview){
			case'1':
				mode1($q_id,$ref,$inpType,$exclusive); //show him the normal preview ( options / reslts )
			break;
			case'2':
				mode2($q_id,$ref,$votedIp,$inpType,$exclusive); //show him the compressed preview ( options & reslts without switching )
			break;
		}
	 }
}

// notAdmin() function calls the appropriate function when the admin is not logged in 
function notAdmin($ref){
	global $connect,$votedIp,$complete_path;
	$complete_path = $_SESSION['complete_path'];
	$hidden=true; // poll visibility
	//session_start();
	if($ref=='wrap-admin'){ // if we are at the admin page
		$adm=@cleanInput('p','u',1);
		$pass=@cleanInput('p','p',1);
		if(empty($adm) || empty($pass)){ //if no one is trying to login as an admin
			if(!empty($_SESSION['pollAdmin'])){
				admContent($ref);
			}else{
				letMeIn($ref);
			}
		}
		
		if((!empty($adm))&&(!empty($pass))){
			aConnect($adm,$pass,$ref); // the admin loggin in process
		}
	}else{	
		$votedOn= 0 ;
		$ques_id= 0 ;

		if (isset($_POST['o'])) {
			$votedOn=$_POST['o'];	
		}

		if (isset($_POST['o'])) {
			$ques_id=$_POST['q'];
		}

		if (count($votedOn)>0 && !empty($ques_id)) {
			$hidden=false;	
		}
		if($hidden){	//no voting has been detected => show the poll
			showPoll($ref);
		}else{	//voting process
			if(!isset($_COOKIE["poll".$ques_id]) || !$votedIp){ // if its a new user (didn't vote already on this poll)
				vote($ques_id,$votedOn,$ref);
			}
		}
	}
}

function mode1($q_id,$ref,$inpType,$exclusive){ //this is the normal preview
	global $connect,$error4,$error5,$strVote,$strResult,$complete_path;
	//show the poll		
	//get the options
	$query="SELECT `id`,`option` FROM `options` WHERE q_id=$q_id";
	$res1=query($connect,$query);
	if($res1){
		$nbr1=$res1->num_rows;
		if($nbr1>0){
			$arr=retrieve_data($res1);
			$length=count($arr);
			echo '<div id="formcontainer-'.$ref.'" ><form method="post" class="pollform" action="'.$_SERVER['PHP_SELF'].'" >';
			echo '<input type="hidden" name="question_id" value="'.$q_id.'" />';
			//display options with radio buttons
			for($i=0;$i<$length;$i++){
				$id=$arr[$i]['id'];
				$value=$arr[$i]['option'];
				$value=str_replace('"', "'", $value);
				echo '<p><input type="'.$inpType.'" name="option" value="'.$value.'" id="option-'.$id.'"/> 
				<label for="option-'.$id.'">'.$value.'</label></p>';
			}

			$location=getUserLocation();
			$countryName=$location[0];
			$countryAbbrev=$location[1];
			$city=$location[2];
			if ($exclusive!=$countryName && $exclusive!="Anybody") {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?result=1"  class="float" id="viewresult-'.$ref.'"><img style="float:left;" src="'.$complete_path.'images/chart.png" alt="'.$strResult.'"/> '.$strResult.'</a></div><div style="clear:both;"></div><br />';
				echo '<span class="msgLayout errorMsg"> Sorry, but only users from <b>'.$exclusive.'</b> can vote on this poll ! </span></form>';
			}else{
				echo '<br /><input id="submitVote-'.$ref.'" class="submitVote" type="submit" value="'.$strVote.'" />';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?result=1"  class="float" id="viewresult-'.$ref.'"><img style="float:left;" src="'.$complete_path.'images/chart.png" alt="'.$strResult.'"/> '.$strResult.'</a></div><div style="clear:both;"></div></form>';
			}

		}else{echo $error4;}
	}else{
			echo $error5;
			exit;
    	 }
}

function mode2($q_id,$ref,$votedIp,$inpType,$exclusive){ //this is the compressed preview
	global $connect,$error4,$error5,$strVote;
	global $strTotal,$error22;
	//get the options
	$query="SELECT `id`,`option`,`votesNumber` FROM `options` WHERE q_id='{$q_id}' order by id asc"; //get the options 
	$res1=query($connect,$query);
	if($res1){
		$nbr1=$res1->num_rows;
		if($nbr1>0){
			$opt_arr=retrieve_data($res1);
			$opt_nbr=count($opt_arr);
			$oTotal=0;//the total of the overriden votes
			$i=0;
			$j=0;
			$va=array(); // overriden votes array
			$vno=array(); //not overiden vote
			foreach($opt_arr as $d){
				if(!empty($d['votesNumber']) || $d['votesNumber']=='0'){
					$oTotal=$oTotal+$d['votesNumber'];
					$vd[$j]=$d['id']; //option id
					$va[$j]=$d['votesNumber'];
					$j++;
				}else{ //the non overriden options case
					$vno[$i]=$d['id'];
					$i++;
				}
			}
			$voNbr=count($va); // overriden votes number	
			$vnoNbr=count($vno); //not overriden votes number	
			echo '<div id="formcontainer-'.$ref.'" ><form method="post" class="pollform" action="'.$_SERVER['PHP_SELF'].'" >';
			echo '<input type="hidden" name="question_id" value="'.$q_id.'" />';
			//display options with radio buttons
				$show='show'; // show the vote and the radio options
				$q2="SELECT options.id, options.option, COUNT(*) as votes FROM votes, options WHERE votes.o_id=options.id AND votes.o_id IN(SELECT id FROM options WHERE q_id='$q_id') GROUP BY votes.o_id order by options.id asc";
				$res2=query($connect,$q2);
				if($res2){
					$res2_arr=retrieve_data($res2);
					$res_nbr2=count($res2_arr);
					if($res_nbr2!=0){ //if there is any legitimate votes
						foreach($res2_arr as $d){ // here we will add the votes that were not overriden to the total votes
							for($l=0;$l<$vnoNbr;$l++){ // we should loop through the options and see the not overriden option
								if($d['id']===$vno[$l]){
									$oTotal=$oTotal+$d['votes']; //add the not overriden votes to the total
								}
							}
						}
					}
					$total=$oTotal;
					if($res_nbr2!=0){ //if legitimate vote was found
						if($_COOKIE["poll".$q_id]=='yes'){
							if($votedIp){ // if the user did vote already
								$show='dont';
							}
						}	
						for($j=0;$j<$opt_nbr;$j++){	
							$id=$opt_arr[$j]['id'];
							$value=$opt_arr[$j]['option'];
							$value=str_replace('"', "'", $value);
							$k=0;
							$votes='';
							for($k;$k<$voNbr;$k++){
								if($id==$vd[$k]){ //if we are talking about the same option then override it's vote result
									$votes=$va[$k];
								}
							}
							if($votes==''){ //if the vote hasn't been change then append to it the original vote from the database
								foreach($res2_arr as $d){ //loop through all the options of the DB and see which one this vote belong to
									$db_optionID=$d['id'];	
									if($id==$db_optionID){
										$votes=$d['votes'];
									}
								}
								if(empty($votes)){ //if no one has voted on this option in the database then
									$votes=0;
								}
							}
							$percent=round(($votes*100)/$total);
							if($show=='show'){
								echo '<p><input type="'.$inpType.'" name="option" value="'.$value.'" id="option-'.$id.'"/> 
								<label for="option-'.$id.'">'.$value.'</label></p>';
							}else{
								echo'<b>'.$value.'</b>';
							}
							echo '<div class="option" ><p><em  class="pollStats">('.$percent.'%, '.$votes.' vote';
							if($votes>1){echo"s";};
							echo ')</em></p>';
							echo '<div class="bar ';
							if($o_id!='skip'){
								if($o_id==$id){
									echo 'yourvote';
									$show='dont';
								}
							}
							echo '" style="width: '.$percent.'%; " ></div></div><br />';
						}
					}else{
						$query="SELECT `id`,`option` FROM `options` WHERE q_id=$q_id";
						$res1=query($connect,$query);
						if($res1){
							$nbr1=$res1->num_rows;
							if($nbr1>0){
								$arr=retrieve_data($res1);
								$length=count($arr);
								//display options with radio/check buttons
								for($i=0;$i<$length;$i++){
									$id=$arr[$i]['id'];
									$value=$arr[$i]['option'];
									$value=str_replace('"', "'", $value);
									echo '<p><input type="'.$inpType.'" name="option" value="'.$value.'" id="option-'.$id.'"/> 
									<label for="option-'.$id.'">'.$value.'</label></p>';
									echo '<div class="option" ><p><em  class="pollStats">(0%, 0 vote)</em></p></div><br />';
								}
							}else{echo $error4;}
						}else{
							echo $error5;
							close($connect);
							exit;
				    	 }
					 }
				}
			echo $strTotal.': '.$total;
			if($show=='show'){
				$location=getUserLocation();
				$countryName=$location[0];
				$countryAbbrev=$location[1];
				$city=$location[2];
				if ($exclusive!=$countryName && $exclusive!="Anybody") {
					echo '<br /><br /><span class="msgLayout errorMsg"> Sorry, but only users from <b>'.$exclusive.'</b> can vote on this poll ! </span>';
				}else{
					echo '<br /><br /><input id="submitVote-'.$ref.'" class="submitVote" type="submit" value="'.$strVote.'" />';
				}	
			}else{echo $show;}
		}else{echo $error4;}
	}else{
		echo $error5;
		exit;
	 }
}
function countryCode2Name($code){
	switch($code){
		case"xx": $name="Anybody"; break;
		case"ad": $name="Andorra"; break;case"ae": $name="United Arab Emirates"; break;case"af": $name="Afghanistan"; break;case"ag": $name="Antigua and Barbuda"; break;case"ai": $name="Anguilla"; break;case"al": $name="Albania"; break;case"am": $name="Armenia"; break;case"an": $name="Netherlands Antilles"; break;case"ao": $name="Angola"; break;case"ar": $name="Argentina"; break;case"as": $name="American Samoa"; break;case"at": $name="Austria"; break;case"au": $name="Australia"; break;case"aw": $name="Aruba"; break;case"ax": $name="Aland Islands"; break;case"az": $name="Azerbaijan"; break;case"ba": $name="Bosnia and Herzegovina"; break;case"bb": $name="Barbados"; break;case"bd": $name="Bangladesh"; break;case"be": $name="Belgium"; break;case"bf": $name="Burkina Faso"; break;case"bg": $name="Bulgaria"; break;case"bh": $name="Bahrain"; break;case"bi": $name="Burundi"; break;case"bj": $name="Benin"; break;case"bm": $name="Bermuda"; break;case"bn": $name="Brunei Darussalam"; break;case"bo": $name="Bolivia"; break;case"br": $name="Brazil"; break;case"bs": $name="Bahamas"; break;case"bt": $name="Bhutan"; break;case"bv": $name="Bouvet Island"; break;case"bw": $name="Botswana"; break;case"by": $name="Belarus"; break;case"bz": $name="Belize"; break;case"ca": $name="Canada"; break;case"cc": $name="Cocos (Keeling) Islands"; break;case"cd": $name="Democratic Republic of the Congo"; break;case"cf": $name="Central African Republic"; break;case"cg": $name="Congo"; break;case"ch": $name="Switzerland"; break;case"ci": $name="Cote D'Ivoire (Ivory Coast)"; break;case"ck": $name="Cook Islands"; break;case"cl": $name="Chile"; break;case"cm": $name="Cameroon"; break;case"cn": $name="China"; break;case"co": $name="Colombia"; break;case"cr": $name="Costa Rica"; break;case"cs": $name="Serbia and Montenegro"; break;case"cu": $name="Cuba"; break;case"cv": $name="Cape Verde"; break;case"cx": $name="Christmas Island"; break;case"cy": $name="Cyprus"; break;case"cz": $name="Czech Republic"; break;case"de": $name="Germany"; break;case"dj": $name="Djibouti"; break;case"dk": $name="Denmark"; break;case"dm": $name="Dominica"; break;case"do": $name="Dominican Republic"; break;case"dz": $name="Algeria"; break;case"ec": $name="Ecuador"; break;case"ee": $name="Estonia"; break;case"eg": $name="Egypt"; break;case"eh": $name="Western Sahara"; break;case"er": $name="Eritrea"; break;case"es": $name="Spain"; break;case"et": $name="Ethiopia"; break;case"fi": $name="Finland"; break;case"fj": $name="Fiji"; break;case"fk": $name="Falkland Islands (Malvinas)"; break;case"fm": $name="Federated States of Micronesia"; break;case"fo": $name="Faroe Islands"; break;case"fr": $name="France"; break;case"ga": $name="Gabon"; break;case"gd": $name="Grenada"; break;case"ge": $name="Georgia"; break;case"gf": $name="French Guiana"; break;case"gh": $name="Ghana"; break;case"gi": $name="Gibraltar"; break;case"gl": $name="Greenland"; break;case"gm": $name="Gambia"; break;case"gn": $name="Guinea"; break;case"gp": $name="Guadeloupe"; break;case"gq": $name="Equatorial Guinea"; break;case"gr": $name="Greece"; break;case"gs": $name="S. Georgia and S. Sandwich Islands"; break;case"gt": $name="Guatemala"; break;case"gu": $name="Guam"; break;case"gw": $name="Guinea-Bissau"; break;case"gy": $name="Guyana"; break;case"hk": $name="Hong Kong"; break;case"hm": $name="Heard Island and McDonald Islands"; break;case"hn": $name="Honduras"; break;case"hr": $name="Croatia (Hrvatska)"; break;case"ht": $name="Haiti"; break;case"hu": $name="Hungary"; break;case"id": $name="Indonesia"; break;case"ie": $name="Ireland"; break;case"il": $name="Israel"; break;case"in": $name="India"; break;case"io": $name="British Indian Ocean Territory"; break;case"iq": $name="Iraq"; break;case"ir": $name="Iran"; break;case"is": $name="Iceland"; break;case"it": $name="Italy"; break;case"jm": $name="Jamaica"; break;case"jo": $name="Jordan"; break;case"jp": $name="Japan"; break;case"ke": $name="Kenya"; break;case"kg": $name="Kyrgyzstan"; break;case"kh": $name="Cambodia"; break;case"ki": $name="Kiribati"; break;case"km": $name="Comoros"; break;case"kn": $name="Saint Kitts and Nevis"; break;case"kp": $name="Korea (North)"; break;case"kr": $name="Korea (South)"; break;case"kw": $name="Kuwait"; break;case"ky": $name="Cayman Islands"; break;case"kz": $name="Kazakhstan"; break;case"la": $name="Laos"; break;case"lb": $name="Lebanon"; break;case"lc": $name="Saint Lucia"; break;case"li": $name="Liechtenstein"; break;case"lk": $name="Sri Lanka"; break;case"lr": $name="Liberia"; break;case"ls": $name="Lesotho"; break;case"lt": $name="Lithuania"; break;case"lu": $name="Luxembourg"; break;case"lv": $name="Latvia"; break;case"ly": $name="Libya"; break;case"ma": $name="Morocco"; break;case"mc": $name="Monaco"; break;case"md": $name="Moldova"; break;case"mg": $name="Madagascar"; break;case"mh": $name="Marshall Islands"; break;case"mk": $name="Macedonia"; break;case"ml": $name="Mali"; break;case"mm": $name="Myanmar"; break;case"mn": $name="Mongolia"; break;case"mo": $name="Macao"; break;case"mp": $name="Northern Mariana Islands"; break;case"mq": $name="Martinique"; break;case"mr": $name="Mauritania"; break;case"ms": $name="Montserrat"; break;case"mt": $name="Malta"; break;case"mu": $name="Mauritius"; break;case"mv": $name="Maldives"; break;case"mw": $name="Malawi"; break;case"mx": $name="Mexico"; break;case"my": $name="Malaysia"; break;case"mz": $name="Mozambique"; break;case"na": $name="Namibia"; break;case"nc": $name="New Caledonia"; break;case"ne": $name="Niger"; break;case"nf": $name="Norfolk Island"; break;case"ng": $name="Nigeria"; break;case"ni": $name="Nicaragua"; break;case"nl": $name="Netherlands"; break;case"no": $name="Norway"; break;case"np": $name="Nepal"; break;case"nr": $name="Nauru"; break;case"nu": $name="Niue"; break;case"nz": $name="New Zealand (Aotearoa)"; break;case"om": $name="Oman"; break;case"pa": $name="Panama"; break;case"pe": $name="Peru"; break;case"pf": $name="French Polynesia"; break;case"pg": $name="Papua New Guinea"; break;case"ph": $name="Philippines"; break;case"pk": $name="Pakistan"; break;case"pl": $name="Poland"; break;case"pm": $name="Saint Pierre and Miquelon"; break;case"pn": $name="Pitcairn"; break;case"pr": $name="Puerto Rico"; break;case"ps": $name="Palestinian Territory"; break;case"pt": $name="Portugal"; break;case"pw": $name="Palau"; break;case"py": $name="Paraguay"; break;case"qa": $name="Qatar"; break;case"re": $name="Reunion"; break;case"ro": $name="Romania"; break;case"ru": $name="Russian Federation"; break;case"rw": $name="Rwanda"; break;case"sa": $name="Saudi Arabia"; break;case"sb": $name="Solomon Islands"; break;case"sc": $name="Seychelles"; break;case"sd": $name="Sudan"; break;case"se": $name="Sweden"; break;case"sg": $name="Singapore"; break;case"sh": $name="Saint Helena"; break;case"si": $name="Slovenia"; break;case"sj": $name="Svalbard and Jan Mayen"; break;case"sk": $name="Slovakia"; break;case"sl": $name="Sierra Leone"; break;case"sm": $name="San Marino"; break;case"sn": $name="Senegal"; break;case"so": $name="Somalia"; break;case"sr": $name="Suriname"; break;case"st": $name="Sao Tome and Principe"; break;case"sv": $name="El Salvador"; break;case"sy": $name="Syria"; break;case"sz": $name="Swaziland"; break;case"tc": $name="Turks and Caicos Islands"; break;case"td": $name="Chad"; break;case"tf": $name="French Southern Territories"; break;case"tg": $name="Togo"; break;case"th": $name="Thailand"; break;case"tj": $name="Tajikistan"; break;case"tk": $name="Tokelau"; break;case"tl": $name="Timor-Leste"; break;case"tm": $name="Turkmenistan"; break;case"tn": $name="Tunisia"; break;case"to": $name="Tonga"; break;case"tr": $name="Turkey"; break;case"tt": $name="Trinidad and Tobago"; break;case"tv": $name="Tuvalu"; break;case"tw": $name="Taiwan"; break;case"tz": $name="Tanzania"; break;case"ua": $name="Ukraine"; break;case"ug": $name="Uganda"; break;case"uk": $name="United Kingdom"; break;case"um": $name="United States Minor Outlying Islands"; break;case"us": $name="United States"; break;case"uy": $name="Uruguay"; break;case"uz": $name="Uzbekistan"; break;case"va": $name="Vatican City State (Holy See)"; break;case"vc": $name="Saint Vincent and the Grenadines"; break;case"ve": $name="Venezuela"; break;case"vg": $name="Virgin Islands (British)"; break;case"vi": $name="Virgin Islands (U.S.)"; break;case"vn": $name="Viet Nam"; break;case"vu": $name="Vanuatu"; break;case"wf": $name="Wallis and Futuna"; break;case"ws": $name="Samoa"; break;case"ye": $name="Yemen"; break;case"yt": $name="Mayotte"; break;case"za": $name="South Africa"; break;case"zm": $name="Zambia"; break;case"zw": $name="Zimbabwe"; break;
	}
		return $name;
}
?>