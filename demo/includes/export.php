<?php
/*------------		this script is for exporting the statistics		--------------*/

session_start();
$adm=$_SESSION['pollAdmin'];
if(!empty($adm))
{
	require_once('dbfns.php');
	global	$hostName ;	global	$userName ;
	global	$password ;global	$database ;
	
	$connect=connect($hostName,$userName,$password,$database);
	if(!$connect){echo'Connection failed ! , please try again later ';exit;}
	
	$type=cleanInput('p','t');
	$id=cleanInput('p','q');
	if(!empty($id))
		{
			$id=explode('q-',$id);
			$id=$id[1]; // the question id
		}
		
	switch($type)
		{
			case'csv':
				csv($id);
				break;
				
			case'xml':
				xml($id);
				break;
		}
}


function csv($id)	// generate the statistics as CSV file to be downloaded 
{
	global $connect;
	
	$file = 'Poll'.$id;
	//the .csv header
	$csv='Vote ID;Option ID;IP;Country;Country Code;City;Vote Date;';
		
	$csv.= "\n";
	$elem=array('id','o_id','ip','country','countryCode','city','voteDate');
	$query="SELECT * from `votes` where q_id='{$id}'";
	$res=query($connect,$query);
	if($res)
		{
			$res_arr=retrieve_data($res);
			//loop through all the elements and generate the file
			for($i=0;$i<count($res_arr);$i++)
				{
					for($j=0;$j<count($elem);$j++)
						{
							$csv.=$res_arr[$i][$elem[$j]].";";
						}
					$csv.= "\n";
				}
		}
	
	//make the file name
	$filename = $file."_".date("Y-m-d_H-i",time());
	//if we want to download the file
		// tell the browser what kind of file is come in
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$filename.".csv");
		header("Content-Transfer-Encoding: binary");
		echo $csv;
		exit;
}


function xml($id)	// Generate the statistics as XML file to be downloaded
{
	global $connect;
	
	$file='Poll'.$id.'_'.date("Y-m-d_H-i",time());
	$xml = "<?xml version=\"1.0\" ?>\n";
	$xml.="<stat>";
	
	//select the votes of the desired poll
	$q="SELECT * from `votes` where q_id='{$id}'";
	$res=query($connect,$q);
	if($res)
		{
			$res_arr=retrieve_data($res);
			$nbr=count($res_arr);
			for($i=0;$i<$nbr;$i++)
				{	//loop through the elements and generate the xml
					$xml.="<id value='".$res_arr[$i]['id']."'>";
					$xml.="<q_id>".$res_arr[$i]['q_id']."</q_id>";
					$xml.="<o_id>".$res_arr[$i]['o_id']."</o_id>";
					$xml.="<ip>".$res_arr[$i]['ip']."</ip>";
					$xml.="<country>".$res_arr[$i]['country']."</country>";
					$xml.="<countryCode>".$res_arr[$i]['countryCode']."</countryCode>";
					$xml.="<city>".$res_arr[$i]['city']."</city>";
					$xml.="<voteDate>".$res_arr[$i]['voteDate']."</voteDate>";
					$xml.="</id>";
				}
		}
	$xml .= "</stat>"; 
	// if we want to download it
		// tell the browser what kind of file is come in
		header("Content-type: text/xml");
		header('Content-Disposition: xml; filename='.$file.'.xml');
		header('Content-Length: ' . strlen($xml));
		header("Content-Transfer-Encoding: binary");
		echo $xml;
		exit;
}


?>