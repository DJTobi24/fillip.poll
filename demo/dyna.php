<?php
header('Content-type: text/css'); 
require_once('includes/dbfns.php');

global	$hostName ;
global	$userName ;
global	$password ;
global	$database ;

$connect=connect($hostName,$userName,$password,$database);

$query = "SELECT `name`,`radioStyle`,`background`,`forground`,`bar`,`width` FROM `areas`";
$res = query($connect,$query);
if($res){
	$resNum = $res->num_rows;
	if($resNum >0){
		$resArr = retrieve_data($res);
		foreach($resArr as $a){
			$name = $a['name'];
			$radioStyle = $a['radioStyle'];
			$background = $a['background'];
			$forground = $a['forground'];
			$bar = $a['bar'];
			$width = $a['width'];
			
			echo "#wrap-$name .radRep{background-image:url('images/radio-$radioStyle.png');}
				  #wrap-$name .chkRep{background-image:url('images/check-$radioStyle.png');}
				
				  #wrap-$name.pollWrapper{
					background: $background;
					width:{$width}px;
					}
					
					#wrap-$name.pollWrapper, #wrap-$name .pollData a.float{color: $forground;}
					#wrap-$name .bar{background:$bar url('images/but-over-hover.png') repeat-x;}
					#wrap-$name .yourvote{background-image: url('images/bar.png');background-repeat:repeat;}
			";
		}
	}
}
?>