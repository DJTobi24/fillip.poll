<?php
/*------------			this is the admin fonctionalities script			---------------*/
@session_start();
$complete_path = realpath("");
$toRep = realpath("..");
$complete_path =  str_replace(array($toRep,'\\'), array("","/"), $complete_path);

$complete_path .='/';

require_once('dbfns.php');

	global	$hostName ;	
	global	$userName ;
	global	$password ;
	global	$database ;

$connect=connect($hostName,$userName,$password,$database);
if(!$connect){echo'Connection failed ! , please try again later ';exit;}

//check out the poll language
$q="SELECT `value` from `params` where name='lang'";
$res=query($connect,$q);
global $strEn,$strFr,$strEs,$strIt,$strDe;
if($res){
	$res_arr=retrieve_data($res);
	$language=$res_arr[0]['value'];
	switch($language){
		case'en':
		require_once('language/en.php');
		$src=$complete_path.'images/flags/us.gif';
		$ttl=$strEn;
		break;
		
		case'fr':
		require_once('language/fr.php');
		$src=$complete_path.'images/flags/fr.gif';
		$ttl=$strFr;
		break;
		
		case'es':
		require_once('language/es.php');
		$src=$complete_path.'images/flags/es.gif';
		$ttl=$strEs;
		break;
		
		case'it':
		require_once('language/it.php');
		$src=$complete_path.'images/flags/it.gif';
		$ttl=$strIt;
		break;
		
		case'de':
		require_once('language/de.php');
		$src=$complete_path.'images/flags/de.gif';
		$ttl=$strDe;
		break;
		
		default:
		require_once('language/en.php');
		$src=$complete_path.'images/flags/us.gif';
		$ttl='English';
		break;
	}
}else{close($connect);die('<span class="msgLayout errorMsg"> Unable to get the poll language !</span>');}

// the aConnect() connects the admin and identify him
// arguments to be passed are (userName,Password)
function aConnect($adm,$pass,$ref){
	global $connect,$error6,$strGoBack,$complete_path;
	$pass=sha1($pass);
	// check if the admin and the pass match.
	$q="SELECT * from `nimda` where nimda='{$adm}' and passwd='{$pass}'";
	$res=query($connect,$q);
	if($res){	//if admin/pass are correct authorize access to the dash board
		$res_nbr=$res->num_rows;
		if($res_nbr==1){
				$_SESSION['pollAdmin']=$adm;
				admContent($ref);//show the dash board
				close($connect);exit;
			}
		else{
			//if the admin userName and the pass dont match
			echo $error6.'
			<a href="#" id="vote-'.$ref.'" class="float link"><img src="'.$complete_path.'images/back.png" />'.$strGoBack.'</a></div><div style="clear:both;"></div>';
		}
	}
	close($connect);
	exit;
}

function newP($ref){ // prints the new poll button on the admin dash board
	global $connect,$strNew,$strNew2,$strQ,$strO,$strAdd,$strRmv,$strPublish,$strBackMenu,$complete_path;
	$q="SELECT `id`,`name` from `areas`";
	$res=query($connect,$q);
	if($res){
		$res_nbr=$res->num_rows;
		if($res_nbr!=0){
			$arr=retrieve_data($res);
		}else{
			$arr=array(array('id'=>'0','name'=>'_not_defined_'));
		}
	}
	echo'
	 <a id="newPoll-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/bigAdd.png" title="'.$strNew2.'" alt="'.$strNew.'"/>'.$strNew.'</a>
	<div class="newPoll" id="np-'.$ref.'">
		<h3> '.$strNew.'</h3>
		<table id="pollTable">
			<tr><td><b><abbr title="Let the user vote on multiple options" style="cursor:help;border-bottom:dotted 1px;">Multiple</abbr>:</b></td><td><select name="multiple" id="multiple" size="1" length="2">
			<option value="no">NO</option>
			<option value="yes">YES</option>
			</select> <br /></td></tr>

			<tr>
				<td>
					<b><abbr title="Make the poll available only to users from a given country (write \'Anybody\' to disable this)" style="cursor:help;border-bottom:dotted 1px;">Target </abbr>:</b>
				</td> 
				<td>
					<input type="text" class="txtInp" id="exclusivePoll" name="x" value="xx"/><br />
				</td>
			</tr>

			<tr><td><b>'.$strQ.':</b></td> <td><input type="text" class="txtInp" name="pollQues"/><br /></td></tr>
			<tr><td><b><abbr title="Where do you want this poll to be shown" style="cursor:help;border-bottom:dotted 1px;">Where</abbr>:</b></td>
			 <td><select name="area" id="areaOption-'.$ref.'">';
			 echo '<option value="0">Choose a section</option>';
			foreach($arr as $a){
				echo '<option value="'.$a['id'].'">'.$a['name'].'</option>';
			}
			$time = gmdate('Y-m-d H:'.strftime('%M').':s');
			$future = strtotime("+1 month");
			$future = gmdate('Y-m-d H:'.strftime('%M').':s',$future);
			echo'</select> <br /></td></tr>
			<tr>
				<td><b>Starts on (GMT) :</b></td>
				<td><input type="text" class="txtInp" name="poll_start" value="'.$time.'"/><br /></td>
			</tr>
			<tr>
				<td><b>Expires on (GMT) :</b></td>
				<td><input type="text" class="txtInp" name="poll_end" value="'.$future.'"/><br /></td>
			</tr>
			<tr><td><b>'.$strO.':</b></td> <td><input class="txtInp pollOptions" type="text" name="pollOpt" /> 1<br /></td></tr>
			<tr><td></td> <td><input type="text" class="txtInp pollOptions" name="pollOpt" /> 2<br /></td></tr>
			<tr><td></td><td><a href="#" id="addPollOpt-'.$ref.'" class="link"><img src="'.$complete_path.'images/add.png" />'.$strAdd.'</a></td></tr>
			<tr><td></td><td><a href="#" id="rmvPollOpt-'.$ref.'" class="link"><img src="'.$complete_path.'images/del.png" />'.$strRmv.' </a></td></tr>
			<tr><td></td><td><a href="#" id="publish-'.$ref.'" name="publish" class="link"><img src="'.$complete_path.'images/publish.png" /> '.$strPublish.'</a></td></tr>
		</table>
		<a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/back.png" />'.$strBackMenu.'</a>
	</div>';
}

function editP($nbr,$q_arr,$ref){		// prints the edit button on the admin dash board
	global $strEdit,$strEdit2,$strEdit3,$strBackMenu,$complete_path;
	echo'<a id="editPoll-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/bigEdit.png" title="'.$strEdit2.'" alt="'.$strEdit.'"/>'.$strEdit.'</a>
	<div class="editPoll" id="ep-'.$ref.'">
		<h3>'.$strEdit3.'</h3>
			<span style="color:#888;font-size:10px;">Choose the Poll that you want to edit</span><br />
			<ol>
		';
		for($i=0;$i<$nbr;$i++){
				$q= htmlentities($q_arr[$i]['question']);
				echo '<a href="#" class="qList" id="q-'.$q_arr[$i]['id'].'">'.$q.'</a><br />';	
			}
		echo'</ol><br />
		<a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/back.png" />'.$strBackMenu.'</a>
	 </div>';
}

function delP($nbr,$q_arr,$ref){		// prints the delete button on the admin dash board
	global $strDel,$strDel2,$strBackMenu,$complete_path;
	echo'
	<a id="delPoll-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/bigDel.png" title="'.$strDel2.'" alt="'.$strDel2.'"/> '.$strDel.' </a>
	<div class="delPoll" id="dp-'.$ref.'">
		<h3> '.$strDel2.' </h3>
		<span style="color:#888;font-size:10px;">Choose the Poll that you want to delete</span><br />
		<ol>';
		for($i=0;$i<$nbr;$i++){
				echo '<a href="#" class="delList" id="q-'.$q_arr[$i]['id'].'">'.htmlentities($q_arr[$i]['question']).'</a><br />';	
			}
		echo'</ol><br /><a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/back.png" />'.$strBackMenu.'</a>
	</div>';
}

function areas($a_nbr,$a_arr,$ref){ // prints the new poll button on the admin dash board
	global $strNew,$strNew2,$strQ,$strO,$strAdd,$strRmv,$strPublish,$strShowPoll,$strBackMenu,$complete_path;
	echo'
	 <a id="newArea-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/areas.png" title="'.$strNew2.'" alt="'.$strNew.'"/> Areas </a>
	<div class="newArea" id="na-'.$ref.'">
		<h3> Area Settings </h3>
		<table id="pollAreaTable">
			<tr><td><b>Areas found:</b></td> <td>'; 
			if($a_nbr==0){echo ' ( No area was found ) ';};
		  echo'</td></tr>
			';
			for($i=0;$i<$a_nbr;$i++){
				echo '<tr><td><a href="#" id="r-'.$a_arr[$i]['id'].'" class="areaRmv" ><img src="'.$complete_path.'images/del.png" alt="remove" title="remove"/></a></td> <td>'.$a_arr[$i]['name'].' </td></tr>';	
			};
			echo'
			<tr><td></td><td><a href="#" id="addArea-'.$ref.'" class="link"><img src="'.$complete_path.'images/add.png" />Add a field</a></td></tr>
			<tr><td></td><td><a href="#" id="rmvArea-'.$ref.'" class="link"><img src="'.$complete_path.'images/del.png" />Remove a field</a></td></tr>
			<tr><td></td><td><a href="#" id="saveArea-'.$ref.'" class="link"><img src="'.$complete_path.'images/save.png" />Save</a></td></tr>
		</table>
		<a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/back.png" />'.$strBackMenu.'</a>
	</div>';
}

function settings($ref){		// prints the login button on the admin dash board
	global $connect,$strLog,$strLog2,$strLog3,$strLog4,$strLog5,$strLog6,$strUser,$strPass,$strSave,$strCancel,$complete_path;
	$q="SELECT `id`,`name` from `areas`";
	$res=query($connect,$q);
	if($res){
		$res_nbr=$res->num_rows;
		if($res_nbr!=0){
			$arr=retrieve_data($res);
		}else{
			$arr=array(array('id'=>'0','name'=>'_not_defined_'));
		}
	}
	echo'
	<a id="nimda_set-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/settings.png" title="'.$strLog2.'" alt="'.$strLog2.'"/> '.$strLog.' </a>
 	 <div class="nimda_set" id="as-'.$ref.'">
	 	 <h3>Settings</h3>
	 	 <b><abbr title="What poll setting\'s you want to edit ?" style="cursor:help;border-bottom:dotted 1px;">What</abbr>:</b><select name="area" id="areaSet-'.$ref.'">';
		foreach($arr as $a){
			echo '<option value="'.$a['id'].'">'.$a['name'].'</option>';
		}
		echo'</select>
 	 	 <h4>'.$strLog4.'</h4><br />
 	 	 <label><input type="radio" name="prev_mode" value="1" /> '.$strLog5.'</label> <br />
 	 	 <label><input type="radio" name="prev_mode" value="2" /> '.$strLog6.'</label> <br /><br />
 	 	 <label >User can vote again after : <input type="text" class="txtInp s" name="expire" id="exp-'.$ref.'" > </label> <abbr style="cursor:help;border-bottom:dotted 1px;"  title="Minutes">Mins</abbr><br />
 	 	 <a href="#" id="changeData-'.$ref.'" class="link"><img src="'.$complete_path.'images/save.png" />'.$strSave.'</a><br />
 	 	 <a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/del.png" />'.$strCancel.'</a><br />
 	 </div>';
}

function loginDetails($admin,$ref){		// prints the login button on the admin dash board
	global $strLog3,$strUser,$strPass,$strSave,$strCancel,$complete_path;
	echo'
	<a id="nimda_logDet-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/lock.png" title="Login Details" alt="Login Details"/> Login info </a>
 	 <div class="nimda_logDet" id="ld-'.$ref.'">
	 	 <h3>Login Details</h3>
	 	 <h4>'.$strLog3.'</h4>
	 	 <table id="pollTable">
	 	 	<tr><td><b><label for="resu-'.$ref.'">'.$strUser.':</label></b></td> <td> <input class="txtInp" type="text" id="resu-'.$ref.'" value="'.$admin.'" /> <br /></td></tr>
	 	 	<tr><td><b><label for="ssap-'.$ref.'">'.$strPass.':</label></b></td> <td> <input class="txtInp" type="password" id="ssap-'.$ref.'" value="****"/> <br /></td></tr>
		 </table><br />
 	 	 <a href="#" id="changeLogin-'.$ref.'" class="link"><img src="'.$complete_path.'images/save.png" />'.$strSave.'</a><br />
 	 	 <a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/del.png" />'.$strCancel.'</a><br />
 	 </div>';
}

function desP($ref){ // prints the design button on the admin dash board
	global $connect,$strDes,$strDes2,$strDes3,$strDes4,$strDes5,$strDes51,$strDes52,$strDes53,$strDes54,$strDes55,$strDefault,$strSave,$strCancel,$complete_path;
 	$q="SELECT `id`,`name` from `areas`";
	$res=query($connect,$q);
	if($res){
		$res_nbr=$res->num_rows;
		if($res_nbr!=0){
			$arr=retrieve_data($res);
		}else{
			$arr=array(array('id'=>'0','name'=>'_not_defined_'));
		}
	}
	 echo'
 	 <a id="design-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/screen.png" title="'.$strDes2.'" alt="'.$strDes2.'"/> '.$strDes.' </a>
 	 <div class="design" id="ds-'.$ref.'">
	 	  <h3>'.$strDes3.'</h3><br />
	 	  <b><abbr title="What area do you want to style ?" style="cursor:help;border-bottom:dotted 1px;">What</abbr>:</b><select name="area" id="areaToStyle-'.$ref.'">';
			foreach($arr as $a){
				echo '<option value="'.$a['id'].'">'.$a['name'].'</option>';
			}
			echo'</select> <br />';
	if($a['id']!=0){
		echo'
			<h4>'.$strDes4.':</h4>
		  <table id="buttonStyle-'.$ref.'">
			  <tr><td><input type="radio" name="buttonStyle" id="style1" value="1"> <label for="style1">Default</label></td><td class="image"><img src="'.$complete_path.'images/radio-1h.png"/></td></tr>
			  <tr><td><input type="radio" name="buttonStyle" id="style2" value="2"> <label for="style2">Sky</label></td><td class="image"><img src="'.$complete_path.'images/radio-2h.png"/></td></tr>
			  <tr><td><input type="radio" name="buttonStyle" id="style3" value="3"> <label for="style3">Water</label></td><td class="image"><img src="'.$complete_path.'images/radio-3h.png"/></td></tr>
			  <tr><td><input type="radio" name="buttonStyle" id="style4" value="4"> <label for="style4">Firefox</label></td><td class="image"><img src="'.$complete_path.'images/radio-4h.png"/></td></tr>
			  <tr><td><input type="radio" name="buttonStyle" id="style5" value="5"> <label for="style5">Safari</label></td><td class="image"><img src="'.$complete_path.'images/radio-5h.png"/></td></tr>
			  <tr><td><input type="radio" name="buttonStyle" id="style6" value="6"> <label for="style6">Soft </label></td><td class="image"><img src="'.$complete_path.'images/radio-6h.png"/></td></tr>
		  </table><br />
		  <h4>'.$strDes5.':</h4>
		  <table >
			  <tr><td>		'.$strDes51.':		</td><td>		<div id="colorSelector-'.$ref.'" class="cs"><div style="background-color: #e1e1e1;">		</div></div> </td></tr>
			  <tr><td>			</td><td> <label><input type="checkbox" id="noPollBG-'.$ref.'" name="trans" value="y" /> I don\'t need a background</label><br />	</td></tr>
			  <tr><td>		'.$strDes52.':		</td><td>		<div id="colorSelector2-'.$ref.'" class="cs"><div style="background-color: #000000;">		</div></div>		</td></tr>
			  <tr><td>		'.$strDes53.':		</td><td>		<div id="colorSelector3-'.$ref.'" class="cs"><div style="background-color: #3399cc;">		</div></div>		</td></tr>
		  </table><br />
		
		  <h4>'.$strDes55.' :</h4>
			  '.$strDes54.': <input class="txtInp s" id="width-'.$ref.'" value="300"/> Px
		  
		 <br /> <a href="#" id="reset-'.$ref.'" class="link"><img src="'.$complete_path.'images/default.png" /> '.$strDefault.'</a><br />
		  <a href="#" id="saveStyle-'.$ref.'" class="link"><img src="'.$complete_path.'images/save.png" />'.$strSave.'</a>
		  ';
	}else{
		echo 'You need to create a poll area first to be able to design it .<br />';
	}
 	 echo '<br /><a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/del.png" />'.$strCancel.'</a></div>';
}

function statP($ref){	// prints the statistics button on the admin dash board
	global $strStat1,$strStat2,$strStat3,$strStat4,$strBackMenu,$complete_path;
	echo' 
	 	<a id="stats-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/activity_monitor.png" title="'.$strStat4.'" alt="'.$strStat4.'"/>'.$strStat3.'</a>
	 	 <div class="stats" id="st-'.$ref.'">
		 	 <h3> Brief Statistics </h3>
		 	 <span style="color:#888;font-size:10px;">View the statistics of the most recent poll</span><br />
		 	 <a href="#" class="statList" id="byIP-'.$ref.'">'.$strStat1.'</a><br />
		 	 <a href="#" class="statList" id="byCTR-'.$ref.'"> '.$strStat2.' </a><br />
		 	 <br /><a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/back.png" />'.$strBackMenu.'</a>
		 </div>';
}

function dnldCSV($nbr,$q_arr,$ref){		// prints the Download CSV button on the admin dash board
	global $strCSV1,$strCSV2,$strBackMenu,$complete_path;
	echo'<a id="csv-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/download.png" title="'.$strCSV1.'" alt="'.$strCSV1.'"/> CSV </a>
	<div class="csv" id="dcsv-'.$ref.'">
		<h3>'.$strCSV2.'</h3>
			<span style="color:#888;font-size:10px;">Choose the Poll that you want to download its complete statistics</span><br />
		<ol>';
		for($i=0;$i<$nbr;$i++){
				echo '<a href="#" class="csvList" id="q-'.$q_arr[$i]['id'].'">'.htmlentities($q_arr[$i]['question']).'</a><br />';	
			}
		echo'</ol><br /><a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/back.png" />'.$strBackMenu.'</a>
	 </div>';
}

function dnldXML($nbr,$q_arr,$ref){		// prints the Download XML button on the admin dash board
	global $strXML1,$strXML2,$strBackMenu,$complete_path;
	echo'<a id="xml-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/download.png" title="'.$strXML1.'" alt="'.$strXML1.'" />XML</a>
	<div class="xml" id="dxml-'.$ref.'">
		<h3>'.$strXML2.'</h3>
			<span style="color:#888;font-size:10px;">Choose the Poll that you want to download its complete statistics</span><br />
		<ol>';
		for($i=0;$i<$nbr;$i++){
				echo '<a href="#" class="xmlList" id="q-'.$q_arr[$i]['id'].'">'.htmlentities($q_arr[$i]['question']).'</a><br />';	
			}
		echo'</ol><br /><a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/back.png" />'.$strBackMenu.'</a>
	 </div>';
}

function dnldPDF($nbr,$q_arr,$ref){		// prints the Download XML button on the admin dash board
	global $strPDF1,$strPDF2,$strBackMenu,$complete_path;
	echo'<a id="pdf-'.$ref.'" class="adBtn"><img src="'.$complete_path.'images/download.png" title="'.$strPDF1.'" alt="'.$strPDF1.'" />PDF</a>
	<div class="pdf" id="dpdf-'.$ref.'">
		<h3>'.$strPDF2.'</h3>
			<span style="color:#888;font-size:10px;">Choose the Poll that you want to download its complete statistics</span><br />
		<ol>';
		for($i=0;$i<$nbr;$i++){
				echo '<a href="'.$complete_path.'exportPDF.php?id='.$q_arr[$i]['id'].'" class="pdfList" target="_blank" >'.htmlentities($q_arr[$i]['question']).'</a><br />';	
			}
		echo'</ol><br /><a href="#" id="vote-'.$ref.'" class="link"><img src="'.$complete_path.'images/back.png" />'.$strBackMenu.'</a>
	 </div><div style="clear:both;"></div>';
}

function admContent($ref){ // the dash board
	global $connect,$src,$ttl,$strWelcome,$strLogout,$strLanguage,$strEn,$strFr,$strEs,$strIt,$strDe,$complete_path;
	if(isset($_SESSION['pollAdmin'])){
			$admin=$_SESSION['pollAdmin'];
		}else{$admin='Unknown';}
	$q="SELECT `id`,`question` FROM `questions` order by id asc";
	$res=query($connect,$q);
	if($res){
		$q_arr=retrieve_data($res);
		$nbr=count($q_arr);
	}


	$q="SELECT `id`,`name` FROM `areas` order by id asc";
	$res=query($connect,$q);
	if($res){
		$a_arr=retrieve_data($res);
		$a_nbr=count($a_arr);
	}
	// if the admin panel is not integrated with 
	echo $strWelcome.' <b>'.ucfirst($admin).'</b> (<span style="cursor:pointer;" id="logout-'.$ref.'">'.$strLogout.'</span>)<br />
		'.$strLanguage.':
		<a href="#" class="iSelect" id="lang-'.$ref.'" title="'.$ttl.'"><img src="'.$src.'" /> '.$ttl.'</a>
		<div id="dd-'.$ref.'" class="dropDown">
			<a href="#" class="langz"><img src="'.$complete_path.'images/flags/de.gif" /> '.$strDe.'</a><br />
			<a href="#" class="langz"><img src="'.$complete_path.'images/flags/us.gif" /> '.$strEn.'</a><br />
			<a href="#" class="langz"><img src="'.$complete_path.'images/flags/fr.gif" /> '.$strFr.'</a><br />
			<a href="#" class="langz"><img src="'.$complete_path.'images/flags/it.gif" /> '.$strIt.'</a><br />
			<a href="#" class="langz"><img src="'.$complete_path.'images/flags/es.gif" /> '.$strEs.'</a><br />
		</div>
	<br /><br />
	<div id="links">';
		newP($ref);					//new poll
		editP($nbr,$q_arr,$ref);		//edit poll
		delP($nbr,$q_arr,$ref);		//delete poll
		areas($a_nbr,$a_arr,$ref);					//new Area
		settings($ref);			//settings
		loginDetails($admin,$ref);
		desP($ref);	// design poll
	 	statP($ref);				//statistics
 		dnldCSV($nbr,$q_arr,$ref);	//download csv statistics
	 	dnldXML($nbr,$q_arr,$ref);	//download xml statistics
	 	dnldPDF($nbr,$q_arr,$ref);	//download xml statistics
	echo'</div>';
}

 function RGB2Hex($r, $g, $b) {	
 	//switch to hex colors 
	 $hex = "#";
	 $hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
	 $hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
	 $hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
	 return $hex;
}
 	
 function Hex2RGB($hex) {
 	//switch to rgb colors
	 $hex = ereg_replace("#", "", $hex);
	 $color = array();

	 if(strlen($hex) == 3) {
		 $color['r'] = hexdec(substr($hex, 0, 1) . $r);
		 $color['g'] = hexdec(substr($hex, 1, 1) . $g);
		 $color['b'] = hexdec(substr($hex, 2, 1) . $b);
	 }
	 else if(strlen($hex) == 6) {
		 $color['r'] = hexdec(substr($hex, 0, 2));
		 $color['g'] = hexdec(substr($hex, 2, 2));
		 $color['b'] = hexdec(substr($hex, 4, 2));
	 }
	 return $color;
 }
 
 //The letMeIn() function generate the inputs to the admin so he can log in
 function letMeIn($ref){
 	global $strLogin,$strGoBack,$strGo,$complete_path;
 	echo"
 	<div id='left'>
		<h2 style='text-shadow:1px 1px 0 #fff'>".$strLogin.":</h2><br />
		<input class='txtInp' type='text' id='resu-".$ref."' /><br />
		<input class='txtInp' type='password' id='ssap-".$ref."'/>
	</div>
	<br /><br /><br /><br />
	<a id='go-".$ref."' class='float link'><img src='".$complete_path."images/lock_open.png' alt='".$strGo."'/>".$strGo."</a><br /><br />
	<a class='float link' href='#' id='vote-".$ref."'><img src='".$complete_path."images/back.png' alt='".$strGoBack."'/>".$strGoBack."</a><div style='clear:both;'></div>
	";
	exit;
 }
 
 //The publish() is the core function of publishing new poll 
 function publish($multiple,$q,$opts,$areaID,$exclusive,$start_on,$expire_on){
 	global $connect,$error7,$done1,$error8,$error9;
 	$time = gmdate('Y-m-d H:'.strftime('%M').':s');
 	// To insert the poll's question
 	$q=html_entity_decode($q);
	//clear any polls that occupy the area
	/*$qU="UPDATE `questions` SET area='_not_defined_' where area='{$areaID}'";
	if(!query($connect,$qU)){
		close($connect);
		die(' '.$error7);
	}*/
	$qQ = "INSERT INTO `questions` (`question`,`qDate`,`area`,`multiple_vote`,`exclusiveTo`,`start_on`,`expire_on`) VALUES('{$q}','{$time}','{$areaID}','{$multiple}','{$exclusive}','{$start_on}','{$expire_on}')";	
	if(query($connect,$qQ)){
		// To get the posted poll's question id
		$qIdQ = "SELECT `id` FROM `questions` WHERE question='{$q}'";
		if($qIdR = query($connect,$qIdQ)){
			$id = retrieve_data($qIdR);
			$id = $id[0]['id'];
			$optnbr=count($opts);
			for($i=0; $i<$optnbr;$i++){
				// insert the options of the poll
				$oQ = "INSERT INTO `options` (`q_id`,`option`) VALUES('{$id}','{$opts[$i]}')";
				query($connect,$oQ);
			}
			echo $done1;
		}else {echo $error8;}
	}else{echo $error9;}
 }

//The modify()  is the core function of editing an existing poll 
 function modify($multiple,$q,$qId,$opts,$votes,$areaID,$exclusive,$start_on,$expire_on){
 	global $connect,$error7,$error10,$error11,$error12,$error13,$done2;
 	$optnbr=count($opts);
 	 $time = gmdate('Y-m-d H:'.strftime('%M').':s');
 	 $q=html_entity_decode($q);
 		// $qU="UPDATE `questions` SET area='_not_defined_' where area='{$areaID}'";
	 	// 	if(!query($connect,$qU)){
			// 	close($connect);
			// 	die(' '.$error7);
			// }
 		$qQ = "UPDATE `questions` SET question='{$q}',qDate='{$time}',area='{$areaID}',multiple_vote='{$multiple}',exclusiveTo='{$exclusive}',start_on='{$start_on}',expire_on='{$expire_on}' where id='{$qId}'";
	if (query($connect,$qQ)){
		// get all the options of the poll
		$oQ = "SELECT * from `options` where q_id='{$qId}'";
		$res=query($connect,$oQ);
		if($res){
				$res_arr=retrieve_data($res);
				$opt_id=array();
				$orig_nbr=count($res_arr);
				for ($j=0;$j<$orig_nbr;$j++){
					$opt_id[$j]=$res_arr[$j]['id'];
				}
			}else {
					echo $error10;
					close($connect);
					exit;
				  }
		for($i=0; $i<$orig_nbr;$i++){
			if (!isset($opts[$i])) {
				$opts[$i]='toBeDeleted';
				$votes[$i]='Original';
			}
			$opts[$i]=html_entity_decode($opts[$i]);
			// Update all the options with their respective new values
			if($votes[$i]=='Original'){ //if the vote is not overriden
				$oQ = "UPDATE `options` SET `option`='{$opts[$i]}',`votesNumber`='' where `id`='{$opt_id[$i]}'";
			}else{
				$oQ = "UPDATE `options` SET `option`='{$opts[$i]}',`votesNumber`='{$votes[$i]}' where `id`='{$opt_id[$i]}'";
			}
			$res=query($connect,$oQ);
		}
		if(!$res){
			echo $error11;close($connect);exit;
		}
		else if($optnbr>$orig_nbr){	// If the admin has added new options
				for($k=$orig_nbr;$k<$optnbr;$k++){
						$option=html_entity_decode($opts[$k]);
						// Insert new options to the poll
						$oQ2 = "INSERT INTO `options` (`q_id`,`option`) VALUES ('{$qId}','{$option}')";
						$res1=query($connect,$oQ2);
					}
				if(!$res1){
						echo $error12;close($connect);exit;
					}
				}
		if($optnbr<$orig_nbr){ //if the admin has removed any option
			$term='toBeDeleted';
			for($k=0;$k<$optnbr;$k++){
				// Delete from the poll the removed options by the admin
				$oQ3 = "DELETE FROM `options` where `option` ='{$term}'";
				$res2=query($connect,$oQ3);
			}
			if(!$res2){
					echo $error13;close($connect);exit;
				}
		}
		echo $done2;
	}
 }
 
 //The decide()  is the function which tells if the poll is new or edited one ,
 // and invoke the proper function ( publish)() / modify() )
 function decide(){
 	global $connect;
 	$multiple=$_POST['m'];
 	switch($multiple){
 		case'yes':
 		$multiple=1;
 		break;
 		case'no':
 		default:
 		$multiple=0;
 		break;
 	}
 	// $q=cleanInput('p','q',1);
 	$q=mysqli_real_escape_string($connect,trim($_POST['q']));
 	$areaID=cleanInput('p','ar',1);
 	$exclusive=cleanInput('p','x',1);
 	//$exclusive=countryName2Code($exclusive);
 	$start_on=cleanInput('p','ps',1);
 	$expire_on=cleanInput('p','pe',1);
	$opts=$_POST['o'];
	$optnbr=count($opts);
	if(!empty($_POST['v'])){
		$votes=$_POST['v'];
	}else{
		$votes='';
	}
	if(!empty($_POST['a'])){
		$show=$_POST['a'];
	}else{
		$show='';
	}
	
	//$ref_arr=explode('wrap-',$ref);
 	//$ref_str=$ref_arr[1];
	for($i=0;$i<$optnbr;$i++){
			//cleans the input (in case of sql injection / unnecessairy characters )
			//$opts[$i]=cleanInput('s',$opts[$i],1);
			$opts[$i]=mysqli_real_escape_string($connect,trim($opts[$i]));
			if(!empty($votes[$i])){
				$votes[$i]=cleanInput('s',$votes[$i],1);
			}
		}
	$save=trim($_POST['s']);
	$qId=trim($_POST['quesId']);
	if($save=='publish'){
			 //if publishing new poll
			publish($multiple,$q,$opts,$areaID,$exclusive,$start_on,$expire_on);
		}else if($save=="save"){ 
					// if editing an existing one 
					modify($multiple,$q,$qId,$opts,$votes,$areaID,$exclusive,$start_on,$expire_on);
				}
	close($connect);
	exit;
 }
 
  //The editPoll()  is the function which print the inputs with the data of the poll ,
 // so we can edit the poll and save it 
 function editPoll($ref){
 	global $connect,$strQ,$strO,$strAdd,$strRmv,$strSave,$strGoBack,$error14,$error15,$complete_path;
 	$q="SELECT `id`,`name` from `areas`";
	$res=query($connect,$q);
	if($res){
		$res_nbr=$res->num_rows;
		if($res_nbr>0){
			$arr=retrieve_data($res);
		}else{
			$arr=array(array('id'=>'0','name'=>'_not_defined_'));
		}
	}
	$id=$_POST['qId'];
	$id=explode('q-',$id);
	$id=$id[1]; // the question id

	$q="SELECT `question`,`exclusiveTo`,`area`,`multiple_vote`,`start_on`,`expire_on` from `questions` where `id`='$id' LIMIT 1";
	$res=query($connect,$q);
	$quesArr=array();
	if($res){
		$res_nbr=$res->num_rows;
		if($res_nbr>0){
			$quesArr=retrieve_data($res);
		}
	}
	$ques=$quesArr[0]['question'];
	$multiple_vote=$quesArr[0]['multiple_vote'];
	$start_on=$quesArr[0]['start_on'];
	$expire_on=$quesArr[0]['expire_on'];
	$area=$quesArr[0]['area'];
	$exclusiveTo=$quesArr[0]['exclusiveTo'];

	$time = gmdate('Y-m-d H:'.strftime('%M').':s');
	// getting all the options of our poll which we are trying to edit
	$oQ = "SELECT * from `options` where q_id='{$id}'";
	if ($oRes = query($connect,$oQ))
	{
		$o_nbr=$oRes->num_rows; // the number of options which the question contains
		if($o_nbr>0)
		{
			$o_arr=retrieve_data($oRes);
			$o_nbr= count($o_arr);
			$i=0;
			$votes=array();
			for($i;$i<$o_nbr;$i++){
				$votes[$i]=$o_arr[$i]['votesNumber'];
				if(empty($votes[$i]) && $votes[$i]!='0'){
					$votes[$i]='Original';
				}
			}
			echo'<div class="newPoll">
					<table id="pollTable">
						<tr>
							<td>
								<b><abbr title="Let the user vote on multiple options" style="cursor:help;border-bottom:dotted 1px;">Multiple</abbr>:</b>
							</td> 
							<td>
								<select name="multiple" id="multiple" size="1" length="2">';
								if (!$multiple_vote) {
									echo'<option value="no">NO</option>
										<option value="yes">YES</option>';
								}else{
									echo'<option value="yes">YES</option>
										 <option value="no">NO</option>';
								}

								echo'</select> <br />
							</td>
						</tr>

						<tr>
							<td>
								<b><abbr title="Make the poll available only to users from a given country ( write \'Anybody\' to disable this )" style="cursor:help;border-bottom:dotted 1px;">Target </abbr>:</b>
							</td> 
							<td>
								<input type="text" class="txtInp" id="exclusivePoll" name="x" value="'.$exclusiveTo.'"/><br />
							</td>
						</tr>

						<tr>
							<td>
								<b>'.$strQ.':</b>
							</td> 
							<td>
								<input type="text" class="txtInp" name="pollQues" value="';$ques=str_replace('"', "'", $ques);echo $ques;echo'"/><br />
							</td>
						</tr>
						<tr><td><b><abbr title="Where do you want this poll to be shown" style="cursor:help;border-bottom:dotted 1px;">Where</abbr>:</b></td> <td><select name="area" id="areaOption-'.$ref.'">';
						foreach($arr as $a){
							if($a['id']==$area){
								echo '<option value="'.$a['id'].'" selected="selected">'.$a['name'].'</option>';
							}else{
								echo '<option value="'.$a['id'].'" >'.$a['name'].'</option>';
							}
						}

						echo'</select> <br /></td></tr>
						<tr>
							<td><b>Starts on (GMT) :</b></td>
							<td><input type="text" class="txtInp" name="poll_start" value="'.$start_on.'"/><br /></td>
						</tr>
						<tr>
							<td><b>Expires on (GMT) :</b></td>
							<td><input type="text" class="txtInp" name="poll_end" value="'.$expire_on.'"/><br /></td>
						</tr>
						<tr>
							<td >
								<b><p style="margin-top:-25px">'.$strO.':</p></b>
							</td>
							 <td>
							 	<input type="text" class="txtInp pollOptions" name="pollOpt" value="';$o_arr[0]['option']=str_replace('"', "'", $o_arr[0]['option']);echo $o_arr[0]['option'];echo'"/> 1<br />
								<abbr title="Put a number to override the original votes or put \'Original\' to keep them" style="cursor:help;border-bottom:dotted 1px;">Votes</abbr> : <input type="text" class="txtInp" name="pollVote" size="13" value="'.$votes[0].'"/> 
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<input type="text" name="pollOpt" class="txtInp pollOptions" value="';$o_arr[1]['option']=str_replace('"', "'", $o_arr[1]['option']);echo $o_arr[1]['option'];echo'"/> 2<br />
								<abbr title="Put a number to override the original votes or put \'Original\' to keep them" style="cursor:help;border-bottom:dotted 1px;">Votes</abbr> : <input type="text" class="txtInp" name="pollVote" size="13" value="'.$votes[1].'"/>
							</td>
						</tr>';
			if($o_nbr>2) // if the options number is bigger than 2 we need to put more inputs 
			{
				for($i=2;$i<$o_nbr;$i++){
						echo'<tr><td></td><td><input type="text" name="pollOpt" class="txtInp pollOptions" value="';$o_arr[$i]['option']=str_replace('"', "'", $o_arr[$i]['option']);echo $o_arr[$i]['option'];echo'"/> '.($i+1).'<br /><abbr title="Put a number to override the original votes or put \'Original\' to keep them" style="cursor:help;border-bottom:dotted 1px;">Votes</abbr> : <input type="text" class="txtInp" name="pollVote" size="13" value="'.$votes[$i].'"/></td></tr>';
					}
			}
					//printing the comands (add/remove an option) and (save)
					echo'<tr><td></td><td><a href="#" id="addPollOpt-'.$ref.'" class="link"><img src="'.$complete_path.'images/add.png" /> '.$strAdd.'</a></td></tr>
						<tr><td></td><td><a href="#" id="rmvPollOpt-'.$ref.'" class="link"><img src="'.$complete_path.'images/del.png" /> '.$strRmv.'</a></td></tr>
						<tr><td></td><td><a href="#" id="publish-'.$ref.'" class='.$id.' name="save"><img src="'.$complete_path.'images/save.png" /> '.$strSave.'</a></td></tr>
						</table><br />
						<a href="#" id="vote-'.$ref.'" class="float"><img src="'.$complete_path.'images/back.png" alt="'.$strGoBack.'"/> '.$strGoBack.'</a><div style="clear:both;"></div>
					</div>';
		}else{echo $error14;exit;}
	}else{echo$error15;}
	close($connect);
	exit;	
 }
 
 
 //The delPoll() is the function which delete the selected poll
 function delPoll($ref){
 	global $connect,$strGoBack,$done3,$error16,$error17,$error18,$complete_path;
 	$id=$_POST['qId'];
		$ques=$_POST['q'];
		$id=explode('q-',$id);
		$id=$id[1]; // the question id
			$qQ = "DELETE from `questions` where id='{$id}'"; // delete the question of the poll we have choosed
			$oQ = "DELETE from `options` where q_id='{$id}'"; // delete the options of the poll we have choosed
			$vQ = "DELETE from `votes` where q_id='{$id}'"; // delete the votes of the poll we have choosed
			if (query($connect,$qQ))
			{
				if (query($connect,$oQ))
				{
					if (query($connect,$vQ))
					{
						echo $done3.'<a href="#" id="vote-'.$ref.'" class="float link"><img src="'.$complete_path.'images/back.png" alt="'.$strGoBack.'"/> '.$strGoBack.' </a><div style="clear:both;"></div>';
					}else{echo $error16;close($connect);exit;}
				}else{echo $error17;close($connect);exit;}
			}else{echo $error18;close($connect);exit;}
		close($connect);
		exit;
 }
 
 //The changeSettings() is the function which change the login information of the admin and the other settings
 function changeSettings($ref,$area) {
 	global $connect,$error19,$error20,$strGoBack,$done7,$complete_path;
 	if(isset($_SESSION['pollAdmin']))
	 	{
	 		$admin=$_SESSION['pollAdmin'];
	 	}else{
	 		die('It seems that there is no admin logged in,changes can\'t be done !');
	 	}
	$prev=cleanInput('p','pr',1);
	$expire=cleanInput('p','x',1);
		if(isset($expire)){
			$query="UPDATE `areas` SET expire='{$expire}',preview='{$prev}' where id='{$area}'";
			$result=query($connect,$query);
			if($result){ // if the expire period was changed then
				echo $done7;	
			}else{echo $error19.' '.mysqli_error($connect);}
		}else{echo $error19;}
		//show the go back button
		echo '<br /><a href="#" id="vote-'.$ref.'" class="float"><img src="'.$complete_path.'images/back.png" alt="'.$strGoBack.'"/>'.$strGoBack.'</a><div style="clear:both;"></div>';
		close($connect);
		exit;
 }
 
 
  //The changeSettings() is the function which change the login information of the admin and the other settings
 function changeLogin($ref) {
 	global $connect,$error19,$error20,$strGoBack,$done7,$complete_path;
 	if(isset($_SESSION['pollAdmin']))
	 	{
	 		$admin=$_SESSION['pollAdmin'];
	 	}else{
	 		die('It seems that there is no admin logged in,changes can\'t be done !');
	 	}
 	
	$u=cleanInput('p','u',1); 
	$p=cleanInput('p','p',1);
	if((!empty($u)) && (!empty($p))){ // if the user and password aren't empty
			if($p=='****') {
					// if the password is 4 stars "****" (then its the default )
					// so the user don't want to change his password
					
					//change the admin username
					$query="UPDATE `nimda` SET nimda='{$u}'";
				}else{ // else the user want to change both the username and the pass
					$p=sha1($p); //encrypt the password
					// change the username and the password
					$query="UPDATE `nimda` SET nimda='{$u}',passwd='{$p}'";
					}
			$result=query($connect,$query);
			if($result){ // if the username and the pass were changed then
				$_SESSION['pollAdmin']=$u; // change the admin session
				echo $done7;
			}else{
				echo $error19;
				}
		}else{
				// the user name and the password were empty !
				echo $error20;
			 }
		//show the go back button
		echo '<br /><a href="#" id="vote-'.$ref.'" class="float"><img src="'.$complete_path.'images/back.png" alt="'.$strGoBack.'"/>'.$strGoBack.'</a><div style="clear:both;"></div>';
		close($connect);
		exit;
 }
 
 
 //The changeStyle() is the function which change the style of the poll
 function changeStyle($ref,$area){
 	global $connect,$done5,$error21,$strGoBack,$complete_path;
 	
	$radStyle=cleanInput('p','radStyle',1);	//the chosen radio button style
	$transparency=cleanInput('p','t',1);    // use a transparency or use a background solid color
	// change the radio style
	
	$bg=cleanInput('p','b',1); // the chosen background color
	if(strstr($bg,'rgb')){
		$bg=explode('rgb(',$bg);
		$bcolors=explode(',',$bg[1]);
		$bgr=$bcolors[0];$bgg=$bcolors[1];$bgb=explode(')',$bcolors[2]);$bgb=$bgb[0];
		$bgColor=RGB2Hex($bgr,$bgg,$bgb);	// switch the color from (red green blue) mode to (hexadecimal) mode
	}else{
		$bgColor=split('#',$bg);
		$bgColor='#'.$bgColor[1];
		$bgColor=str_replace(';','',$bgColor);
	}
	//change the backGround color 
	if($transparency=='y'){
		$bg="transprnt";
	}else{
		$bg=$bgColor;
	}
	
	$fg=cleanInput('p','f',1); // the chosen text color
	$fcolors=explode(',',$fg);
	if(strstr($fcolors[0],'rgb('))
		{
			$fgr=explode('rgb(',$fcolors[0]);$fgr=$fgr[1];$fgg=$fcolors[1];
			$fgb=explode(')',$fcolors[2]);$fgb=$fgb[0];
			$fgColor=RGB2Hex($fgr,$fgg,$fgb);
		}else{
		$fgColor=explode('#',$fg);
		$fgColor='#'.$fgColor[1];
		$fgColor=str_replace(';','',$fgColor);
		}
	
	$vg=cleanInput('p','v',1); // the chosen vote bar color
	$vcolors=explode(',',$vg);
	if(strstr($vcolors[0],'rgb('))
		{
			$vgr=explode('rgb(',$vcolors[0]);$vgr=$vgr[1];$vgg=$vcolors[1];
			$vgb=explode(')',$vcolors[2]);$vgb=$vgb[0];
			$vgColor=RGB2Hex($vgr,$vgg,$vgb);
		}else{
			$vgColor=split('#',$vg);
			$vgColor='#'.$vgColor[1];
			$vgColor=str_replace(';','',$vgColor);
		}
	
	$width=cleanInput('p','w',1); // the chosen width
	// change the width of the poll
	if(!empty($width) && is_int($width))
		{
			$w=$width;
		}else{
			$w=500;
		}

	$q="UPDATE `areas` set radioStyle='{$radStyle}',background='{$bg}',forground='{$fgColor}',bar='{$vgColor}',width='{$width}' where id='$area'";
	$res=query($connect,$q);
	
	if($res)	// if all the color have been changed then
	{
		echo $done5;
	}else
		{
			echo $error21;
		}
	//show the go back button
	echo"<br /><a href='#' id=\"vote-".$ref."\" class=\"float link\"><img src=\"".$complete_path."images/back.png\" alt=\"".$strGoBack."\"/>".$strGoBack."</a><div style=\"clear:both;\"></div>";
	close($connect);
	exit;
 }
 
 //The byIP() is the function which show the statistics of the votes by ip
 function byIP($qId,$ref){
 	global $connect,$strIP,$strTime,$strGoBack,$error22,$complete_path;
 	// show the 25 recent votes which have voted on the current shown poll
 	$q="SELECT * from `votes` where q_id='{$qId}' Order By `voteDate` desc LIMIT 25";
		$res=query($connect,$q);
		if($res) // if we could get the data 
		{
			$res_arr=retrieve_data($res);
			$res_nbr=count($res_arr);
			echo'<h3>'.$strIP.'</h3>';
			echo"<table id='ip'>
			<th><tr><td class='ip'><b> IP </b></td><td class='time'><b> ".$strTime."</b><span style='color:#888;font-size:10px;'> (GMT)</span> </td></tr></th>";
			if($res_nbr>0)	//if we found any votes
				{
					for($i=0;$i<$res_nbr;$i++)
					{
						echo "<tr><td class='ip'>".$res_arr[$i]['ip']."</td><td class='time'>".$res_arr[$i]['voteDate']."</td></tr>";
					}
					echo"</table>";
				}else{	// else if we didnt find any one 
						echo $error22;
					 }
		}
	echo"<br /><a href='#' id=\"vote-".$ref."\" class=\"float link\"><img src=\"".$complete_path."images/back.png\" alt=\"".$strGoBack."\"/>".$strGoBack."</a><div style=\"clear:both;\"></div>";
	close($connect);
	exit;
 }
 
 //The byCTR() is the function which show the statistics of the votes by country
 function byCTR($qId,$ref){
 	global $connect,$strGoBack,$strCtr,$strTotal2,$error23,$error24,$complete_path;
 	
 	//select and group the countries by the nuber of votes in each country
 	$q="SELECT countryCode,country, COUNT(*) AS total FROM `votes` where q_id='{$qId}'
				GROUP BY countryCode ORDER BY total DESC LIMIT 15";
	$result = query($connect,$q);
	if($result)	//if we could select and group them 
		{
			echo"<h3>".$strCtr."</h3>";
			$sum=0;
			$res_arr=retrieve_data($result);
			$res_nbr=count($res_arr);
			if($res_nbr>0)
				{
					echo'<br /><table id="byCTR" class="byCountry">';
					for($i=0;$i<$res_nbr;$i++)
						{
							// Show the countries
							echo'<tr><td class="flag"><img src="'.$complete_path.'images/flags/'.strtolower($res_arr[$i]["countryCode"]).'.gif" /></td>
							<td class="country">'.$res_arr[$i]["country"].'</td>
							<td class="people">'.$res_arr[$i]["total"].'</td></tr>';
							$sum=$sum+$res_arr[$i]["total"];
						}
						echo'</table><br /><b>'.$strTotal2.' :</b>'.$sum;
				}else{
						// if no one has voted yet
						echo $error23;
					}
		}else{echo $error24;}
		
		//show the goBack button
		echo"<a href='#' id=\"vote-".$ref."\" class=\"float link\"><img src=\"".$complete_path."images/back.png\" alt=\"".$strGoBack."\"/> ".$strGoBack."</a><div style=\"clear:both;\"></div>";
	close($connect);
	exit;
 }
 
 //The stats() is the function which decide if the admin wants to see the votes by ip or by country
 // and invoke the necessary function
 function stats($ref){
 	global $connect,$error25;
	$type=$_POST['type'];
	$type=substr($type,0,5);
	// select the current poll
	$q="SELECT `id` from `questions` order by qDate desc LIMIT 1 ";
	$res=query($connect,$q);
	if($res)
	{
		$res_arr=retrieve_data($res);
		$qId=$res_arr[0]['id']; //get the poll id
	}else{echo $error25;close($connect);exit;}
	switch($type)
		{
		case'byIP-':	// when showing votes by ip
			byIP($qId,$ref);
			break;
		
		case'byCTR': // when showing votes by copuntry
			byCTR($qId,$ref);
			break;
		}
 }
 
  //The resetStyle() is the function which takes the poll design to its
 // initial default design
 function resetStyle($ref,$area){
 	global $connect,$strGoBack,$error26,$done6,$complete_path;
 	
 	// reset the poll default settings
	$q="UPDATE `areas` set radioStyle='1',background='#e1e1e1',forground='#000000',bar='#3399cc',width='300',preview='1',expire='43200' where id='$area'";
	
	if(query($connect,$q))
		{// if every reset have been made 
			echo $done6;	
		}else{echo $error26;}
	echo"<br /><a href='#' id=\"vote-".$ref."\" class=\"float link\"><img src=\"".$complete_path."images/back.png\" alt=\"".$strGoBack."\"/> ".$strGoBack."</a><div style=\"clear:both;\"></div>";
	close($connect);
	exit;	
 }

// the changeLang function change the language of the poll
 function changeLang($ref){
 	global $connect,$strMsg,$error27,$strGoBack,$complete_path;
 	$language=trim($_POST['l']);
 	switch($language)
	 	{
	 		case'English':
	 		case'Anglais':
	 		case'Inglés':
	 		case'Inglese':
	 		case'Englisch':
	 		$abbrv='en';
	 		break;
	 		
	 		case'French':
	 		case'Français':
	 		case'Francés':
	 		case'Francese':
	 		case'Französisch':
	 		$abbrv='fr';
	 		break;
	 		
	 		case'Spanish':
	 		case'Espagnol':
	 		case'Español':
	 		case'Spagnolo':
	 		case'Spanisch':
	 		$abbrv='es';
	 		break;
	 		
	 		case'Italian':
	 		case'Italien':
	 		case'Italiano':
	 		case'Italienisch':
	 		$abbrv='it';
	 		break;
	 		
	 		case'German':
	 		case'Allemand':
	 		case'Alemán':
	 		case'Germano':
	 		case'Deutsch':
	 		$abbrv='de';
	 		break;
	 		
	 		default:
	 		$abbrv='en';
	 		break;
	 	}
 	// change the poll language
	$q="UPDATE `params` set value='{$abbrv}' where name='lang'";
	if(query($connect,$q))
		{
			echo'<span class="msgLayout doneMsg">'.$strMsg.' <b>'.$language.'</b> </span>';	
		}else{
				echo $error27;
			 }
	echo"<br /><a href='#' id=\"vote-".$ref."\" class=\"float link\"><img src=\"".$complete_path."images/back.png\" alt=\"".$strGoBack."\"/> ".$strGoBack."</a><div style=\"clear:both;\"></div>";
	close($connect);
	exit;
 }

 function manageArea(){
 	global $connect;
 	$areas=$_POST['a'];
 	$nbr=count($areas);
 	for ($i=0; $i < $nbr; $i++) { 
 		$areaName=html_entity_decode(cleanInput('s',$areas[$i],1));
 		$q = "INSERT INTO `areas` (`name`,`radioStyle`,`background`,`forground`,`bar`,`width`,`preview`,`expire`) VALUES('{$areaName}','1','#e1e1e1','#000000','#3399cc','300','1','43200')";
 		$res=query($connect,$q);
 	}
	if($res){
		echo'<span class="msgLayout doneMsg">The poll areas has been saved.</span>';
	}else{echo'<span class="msgLayout doneMsg"> The poll areas can\'t be saved </span>';}
	exit;
 }
 
 //delete an area
 function delArea(){
 	global $connect;
 	$id=$_POST['aid'];
 	$id=explode('-',$id);
 	$id=$id[1];
	$q = "DELETE from `areas` where id= '$id'";
	query($connect,$q);
 }

// the switcher() is the function which distribute the commands 
//sent by ajax to invoke the right function
function switcher($command,$ref){
 	switch($command){
	case'letMeIn':	
		// when trying to login as admin
		letMeIn($ref);
		break;
		
	case'getMeOut':	// When trying to logout
		$_SESSION['pollAdmin']='';
		session_destroy();
		if($ref=='wrap-admin'){
			notAdmin($ref);
		}else{
			showPoll($ref);
		}
		exit;
		break;
		
	// publishing / editing process a poll
	case'publish':		
		// decide if it's a new poll or an edited one ,
		// and do the necessairy to handle it
		decide(/*$ref*/);	 
		break;
		
	// showing the poll details (inputs) to edit them
	case'editPoll':		
		editPoll($ref); 
		break;
		
	// getting the list of the polls to delete
	case'delPoll':
		delPoll($ref);
		break;
		
	// when trying to change the login of the admin (username/pass)
	case'changeData':
		global $connect;
		$area=cleanInput('p','a',1);
		changeSettings($ref,$area);
		break;
	
	// when trying to change the login of the admin (username/pass)
	case'changeLogin':
		changeLogin($ref);
		break;	
		
	// when adding /editing /deleteing areas
	case'saveArea':
		manageArea($ref);
	break;
	
	case'delArea':
		delArea($ref);
	break;	
	
	// when changing the design of the poll
	case'changeStyle':	
		global $connect;
		$area=cleanInput('p','av',1);
		changeStyle($ref,$area);
		break;	
	
	case'stats':	// getting the list of statistics 
		stats($ref);
		break;
		
	case'resetStyle':	// in case we want the default style of the poll
		global $connect;
		$area=cleanInput('p','v',1);
		resetStyle($ref,$area);
		break;	
		
	case'changeLang':	// change the language of the poll 
		changeLang($ref);
		break;	
	}
}

/*function countryName2Code($name){
	switch($name){
	case"Anybody":$code="xx";break;case"Andorra": $code="ad"; break;case"United Arab Emirates": $code="ae"; break;case"Afghanistan": $code="af"; break;case"Antigua and Barbuda": $code="ag"; break;case"Anguilla": $code="ai"; break;case"Albania": $code="al"; break;case"Armenia": $code="am"; break;case"Netherlands Antilles": $code="an"; break;case"Angola": $code="ao"; break;case"Argentina": $code="ar"; break;case"American Samoa": $code="as"; break;case"Austria": $code="at"; break;case"Australia": $code="au"; break;case"Aruba": $code="aw"; break;case"Aland Islands": $code="ax"; break;case"Azerbaijan": $code="az"; break;case"Bosnia and Herzegovina": $code="ba"; break;case"Barbados": $code="bb"; break;case"Bangladesh": $code="bd"; break;case"Belgium": $code="be"; break;case"Burkina Faso": $code="bf"; break;case"Bulgaria": $code="bg"; break;case"Bahrain": $code="bh"; break;case"Burundi": $code="bi"; break;case"Benin": $code="bj"; break;case"Bermuda": $code="bm"; break;case"Brunei Darussalam": $code="bn"; break;case"Bolivia": $code="bo"; break;case"Brazil": $code="br"; break;case"Bahamas": $code="bs"; break;case"Bhutan": $code="bt"; break;case"Bouvet Island": $code="bv"; break;case"Botswana": $code="bw"; break;case"Belarus": $code="by"; break;case"Belize": $code="bz"; break;case"Canada": $code="ca"; break;case"Cocos (Keeling) Islands": $code="cc"; break;case"Democratic Republic of the Congo": $code="cd"; break;case"Central African Republic": $code="cf"; break;case"Congo": $code="cg"; break;case"Switzerland": $code="ch"; break;case"Cote D'Ivoire (Ivory Coast)": $code="ci"; break;case"Cook Islands": $code="ck"; break;case"Chile": $code="cl"; break;case"Cameroon": $code="cm"; break;case"China": $code="cn"; break;case"Colombia": $code="co"; break;case"Costa Rica": $code="cr"; break;case"Serbia and Montenegro": $code="cs"; break;case"Cuba": $code="cu"; break;case"Cape Verde": $code="cv"; break;case"Christmas Island": $code="cx"; break;case"Cyprus": $code="cy"; break;case"Czech Republic": $code="cz"; break;case"Germany": $code="de"; break;case"Djibouti": $code="dj"; break;case"Denmark": $code="dk"; break;case"Dominica": $code="dm"; break;case"Dominican Republic": $code="do"; break;case"Algeria": $code="dz"; break;case"Ecuador": $code="ec"; break;case"Estonia": $code="ee"; break;case"Egypt": $code="eg"; break;case"Western Sahara": $code="eh"; break;case"Eritrea": $code="er"; break;case"Spain": $code="es"; break;case"Ethiopia": $code="et"; break;case"Finland": $code="fi"; break;case"Fiji": $code="fj"; break;case"Falkland Islands (Malvinas)": $code="fk"; break;case"Federated States of Micronesia": $code="fm"; break;case"Faroe Islands": $code="fo"; break;case"France": $code="fr"; break;case"Gabon": $code="ga"; break;case"Grenada": $code="gd"; break;case"Georgia": $code="ge"; break;case"French Guiana": $code="gf"; break;case"Ghana": $code="gh"; break;case"Gibraltar": $code="gi"; break;case"Greenland": $code="gl"; break;case"Gambia": $code="gm"; break;case"Guinea": $code="gn"; break;case"Guadeloupe": $code="gp"; break;case"Equatorial Guinea": $code="gq"; break;case"Greece": $code="gr"; break;case"S. Georgia and S. Sandwich Islands": $code="gs"; break;case"Guatemala": $code="gt"; break;case"Guam": $code="gu"; break;case"Guinea-Bissau": $code="gw"; break;case"Guyana": $code="gy"; break;case"Hong Kong": $code="hk"; break;case"Heard Island and McDonald Islands": $code="hm"; break;case"Honduras": $code="hn"; break;case"Croatia (Hrvatska)": $code="hr"; break;case"Haiti": $code="ht"; break;case"Hungary": $code="hu"; break;case"Indonesia": $code="id"; break;case"Ireland": $code="ie"; break;case"Israel": $code="il"; break;case"India": $code="in"; break;case"British Indian Ocean Territory": $code="io"; break;case"Iraq": $code="iq"; break;case"Iran": $code="ir"; break;case"Iceland": $code="is"; break;case"Italy": $code="it"; break;case"Jamaica": $code="jm"; break;case"Jordan": $code="jo"; break;case"Japan": $code="jp"; break;case"Kenya": $code="ke"; break;case"Kyrgyzstan": $code="kg"; break;case"Cambodia": $code="kh"; break;case"Kiribati": $code="ki"; break;case"Comoros": $code="km"; break;case"Saint Kitts and Nevis": $code="kn"; break;case"Korea (North)": $code="kp"; break;case"Korea (South)": $code="kr"; break;case"Kuwait": $code="kw"; break;case"Cayman Islands": $code="ky"; break;case"Kazakhstan": $code="kz"; break;case"Laos": $code="la"; break;case"Lebanon": $code="lb"; break;case"Saint Lucia": $code="lc"; break;case"Liechtenstein": $code="li"; break;case"Sri Lanka": $code="lk"; break;case"Liberia": $code="lr"; break;case"Lesotho": $code="ls"; break;case"Lithuania": $code="lt"; break;case"Luxembourg": $code="lu"; break;case"Latvia": $code="lv"; break;case"Libya": $code="ly"; break;case"Morocco": $code="ma"; break;case"Monaco": $code="mc"; break;case"Moldova": $code="md"; break;case"Madagascar": $code="mg"; break;case"Marshall Islands": $code="mh"; break;case"Macedonia": $code="mk"; break;case"Mali": $code="ml"; break;case"Myanmar": $code="mm"; break;case"Mongolia": $code="mn"; break;case"Macao": $code="mo"; break;case"Northern Mariana Islands": $code="mp"; break;case"Martinique": $code="mq"; break;case"Mauritania": $code="mr"; break;case"Montserrat": $code="ms"; break;case"Malta": $code="mt"; break;case"Mauritius": $code="mu"; break;case"Maldives": $code="mv"; break;case"Malawi": $code="mw"; break;case"Mexico": $code="mx"; break;case"Malaysia": $code="my"; break;case"Mozambique": $code="mz"; break;case"Namibia": $code="na"; break;case"New Caledonia": $code="nc"; break;case"Niger": $code="ne"; break;case"Norfolk Island": $code="nf"; break;case"Nigeria": $code="ng"; break;case"Nicaragua": $code="ni"; break;case"Netherlands": $code="nl"; break;case"Norway": $code="no"; break;case"Nepal": $code="np"; break;case"Nauru": $code="nr"; break;case"Niue": $code="nu"; break;case"New Zealand (Aotearoa)": $code="nz"; break;case"Oman": $code="om"; break;case"Panama": $code="pa"; break;case"Peru": $code="pe"; break;case"French Polynesia": $code="pf"; break;case"Papua New Guinea": $code="pg"; break;case"Philippines": $code="ph"; break;case"Pakistan": $code="pk"; break;case"Poland": $code="pl"; break;case"Saint Pierre and Miquelon": $code="pm"; break;case"Pitcairn": $code="pn"; break;case"Puerto Rico": $code="pr"; break;case"Palestinian Territory": $code="ps"; break;case"Portugal": $code="pt"; break;case"Palau": $code="pw"; break;case"Paraguay": $code="py"; break;case"Qatar": $code="qa"; break;case"Reunion": $code="re"; break;case"Romania": $code="ro"; break;case"Russian Federation": $code="ru"; break;case"Rwanda": $code="rw"; break;case"Saudi Arabia": $code="sa"; break;case"Solomon Islands": $code="sb"; break;case"Seychelles": $code="sc"; break;case"Sudan": $code="sd"; break;case"Sweden": $code="se"; break;case"Singapore": $code="sg"; break;case"Saint Helena": $code="sh"; break;case"Slovenia": $code="si"; break;case"Svalbard and Jan Mayen": $code="sj"; break;case"Slovakia": $code="sk"; break;case"Sierra Leone": $code="sl"; break;case"San Marino": $code="sm"; break;case"Senegal": $code="sn"; break;case"Somalia": $code="so"; break;case"Suriname": $code="sr"; break;case"Sao Tome and Principe": $code="st"; break;case"El Salvador": $code="sv"; break;case"Syria": $code="sy"; break;case"Swaziland": $code="sz"; break;case"Turks and Caicos Islands": $code="tc"; break;case"Chad": $code="td"; break;case"French Southern Territories": $code="tf"; break;case"Togo": $code="tg"; break;case"Thailand": $code="th"; break;case"Tajikistan": $code="tj"; break;case"Tokelau": $code="tk"; break;case"Timor-Leste": $code="tl"; break;case"Turkmenistan": $code="tm"; break;case"Tunisia": $code="tn"; break;case"Tonga": $code="to"; break;case"Turkey": $code="tr"; break;case"Trinidad and Tobago": $code="tt"; break;case"Tuvalu": $code="tv"; break;case"Taiwan": $code="tw"; break;case"Tanzania": $code="tz"; break;case"Ukraine": $code="ua"; break;case"Uganda": $code="ug"; break;case"United Kingdom": $code="uk"; break;case"United States Minor Outlying Islands": $code="um"; break;case"United States": $code="us"; break;case"Uruguay": $code="uy"; break;case"Uzbekistan": $code="uz"; break;case"Vatican City State (Holy See)": $code="va"; break;case"Saint Vincent and the Grenadines": $code="vc"; break;case"Venezuela": $code="ve"; break;case"Virgin Islands (British)": $code="vg"; break;case"Virgin Islands (U.S.)": $code="vi"; break;case"Viet Nam": $code="vn"; break;case"Vanuatu": $code="vu"; break;case"Wallis and Futuna": $code="wf"; break;case"Samoa": $code="ws"; break;case"Yemen": $code="ye"; break;case"Mayotte": $code="yt"; break;case"South Africa": $code="za"; break;case"Zambia": $code="zm"; break;case"Zimbabwe": $code="zw"; break;
	}
	return $code;
}*/
?>