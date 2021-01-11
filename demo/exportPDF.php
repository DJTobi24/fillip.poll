<?php
/*-------------		The script of generating and downloading a PDF	-----------------*/

if (!class_exists("PDFlib")) {
	die("ERROR : You need to have 'PDFlib' class installed to generate a PDF");
}

session_start();
$adm=$_SESSION['pollAdmin'];
if(!empty($adm))
{
	require_once('includes/dbfns.php');	//the basic poll functionalities
	$id=cleanInput('g','id');
	if(empty($id))
		{
			echo'Error:The id of the required pdf is null !';exit;
		}

	global	$hostName ;	
	global	$userName ;
	global	$password ;
	global	$database ;

	$connect=connect($hostName,$userName,$password,$database);
	if(!$connect){echo'Connection failed ! , please try again later ';exit;}
	
	$searchpath = $_SERVER['DOCUMENT_ROOT']."/smooth_poll/images/flags/" ;
	if(!($p = new PDFlib()))
		{
			echo "Error : The PDF can't be generated ! , it seems that this host dont support the <b>'PDFlib'</b> library ";
			close($connect);
			exit;
		}
    $p->set_parameter("errorpolicy", "return"); // check return values of load_font() etc.
    $p->set_parameter("hypertextencoding", "winansi"); // used to prevent problems with japanese systems
    $p->set_parameter("SearchPath", $searchpath); // **set search path parameter in pdf
    if ($p->begin_document("", "") == 0) {
		close($connect);
       die("Error: " . $p->get_errmsg());
   }
	   $p->set_info("Creator", " Smooth Ajax Poll"); //the creator
	   $p->set_info("Author", " Poll Statistics "); // the author
	   $p->set_info("Title", " Complete Poll Statistics ");// the title
	   $p->begin_page_ext(11*72, 8.5*72, "");  // declare page with standard letter size
			$font = $p->load_font("Times-Roman", "host", ""); // loading a font
		    $p->setfont( $font, 35); // font size
		    $p->set_text_pos( 280, 560); // the text position
			$p->show('Country Statistics'); 
		
			$p->setfont( $font, 20);
			$p->set_text_pos( 150, 500);
			$p->show('Country');
			$p->set_text_pos( 350, 500);
			$p->show('Votes');
			$p->set_text_pos( 550, 500);
			$p->show('Percentage');
		
			$p->setfont( $font, 14);
			// count all the votes ,group them by country and sort them 
			$query="SELECT countryCode,country, COUNT(*) AS total FROM `votes` where q_id='{$id}'
				GROUP BY countryCode ORDER BY total DESC";
			$res=query($connect,$query);
			$res_nbr=$res->num_rows;
			if($res_nbr>0)  //if any result were found 
			{
				$arr=retrieve_data($res);
				$nbr=count($arr);
				$total=0;
				for($i=0;$i<$nbr;$i++)
					{
						$total+=$arr[$i]['total']; // count the total of the votes
					}
				$query="SELECT countryCode,country,COUNT(*) AS total FROM `votes` where q_id='{$id}'
					GROUP BY countryCode ORDER BY total DESC LIMIT 14";
					
				$res=query($connect,$query);
				$arr=retrieve_data($res);
			
				$nbr=count($arr);
				for($i=0;$i<$nbr;$i++)
				{
					$flag=$arr[$i]['countryCode']; //the country code 
					$country=$arr[$i]['country']; // the country
					$votes=$arr[$i]['total']; // the votes of that country
					$y=470-$i*20;
					
					$img = $flag.".gif";  // your image name
			   		$image = $p->load_image("gif", $img, ""); // select the flag of the country
					if(!$image)
			   			{
			   				close($connect);
			   				die("Error: " . $p->get_errmsg());
			   			}
			   		$p->fit_image($image, 135,$y, ""); //place image within page coordinates
					$p->set_text_pos( 165, $y);
					$p->show($country);
					$p->set_text_pos( 365, $y);
					$p->show($votes);
				}
			    $p->close_image($image);   // close image object
			    
			    $full=0;
				 for($i=0;$i<$nbr;$i++)
				{
					$votes=$arr[$i]['total'];
					$percent=$votes*100/$total; // get the percent of the votes
					$full+=$percent; // the total percentage
					$y=470-$i*20; // the y position
					$p->set_text_pos( 570, $y);
					$p->show(round($percent,2)." %"); // show the percent with 2 numbers after the comma 
					if($i==$nbr-1)
						{
							if($full!=100) //if the total isn't 100 %
								{
									$left=100-round($full,2);
									$leftp=$left/100;
									$leftVotes=$leftp*$total;
									$p->set_text_pos( 165, $y-20);
									$p->show('Others');
									$p->set_text_pos( 365, $y-20);
									$p->show($leftVotes);
									$p->set_text_pos( 570, $y-20);
									$p->show($left." %");
								}
						}
				}	
			    
				$p->setfont( $font, 12);
				$p->set_text_pos( 100, 160);
				$p->show('Total votes :'.$total);
				
				$query="SELECT * FROM `questions` where id='{$id}'";
				$res=query($connect,$query);
				$arr=retrieve_data($res);
				$question=$arr[0]['question'];
					
				$p->setfont( $font, 12);
				$p->set_text_pos( 100, 130);
				$p->show('Poll : " '.$question.' "');
				
				$p->setfont( $font, 10);
				$query="SELECT * FROM `options` where q_id='{$id}' order by id asc";
				$res=query($connect,$query);
				$opt_arr=retrieve_data($res);
				$opt_nbr=count($opt_arr);
				
				for($i=0;$i<$opt_nbr;$i++)
					{
						$options=$opt_arr[$i]['option'];
						$y=115-$i*15;
						$p->set_text_pos( 120,$y);
						$j=$i+1;
						$p->show('Option'.$j.': " '.$options.' "');
					}
			$p->end_page_ext(""); //end the 1st page
			$p->begin_page_ext(11*72, 8.5*72, ""); // begin the seconde page
				$font = $p->load_font("Times-Roman", "host", "");
			    $p->setfont( $font, 35);
			    $p->set_text_pos( 280, 560);
				$p->show('Options statistics');
			
				$p->setfont( $font, 20);
				$p->set_text_pos( 150, 500);
				$p->show('Option ID');
				$p->set_text_pos( 350, 500);
				$p->show('Votes');
				$p->set_text_pos( 550, 500);
				$p->show('Percentage');
			
			
				
				$query="SELECT o_id,COUNT(*) AS total FROM `votes` where q_id='{$id}' group by o_id order by o_id asc";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$font = $p->load_font("Times-Roman", "host", "");
		   		$p->setfont( $font, 14);
		   		$nbr=count($res_arr);
				for($i=0;$i<$nbr;$i++)
				{
					$vote=$res_arr[$i]['total'];
					$o_id=$res_arr[$i]['o_id'];
					$percent=$vote*100/$total;
					$y=470-$i*20;
					$p->set_text_pos( 175, $y);
					$p->show($o_id);
					$p->set_text_pos( 365, $y);
					$p->show($vote);
					$p->set_text_pos( 570, $y);
					$p->show(round($percent,2)." %");
				}
				$y=$y-20;
				for($k=0;$k<$opt_nbr;$k++)
				{
					$p->setfont( $font, 10);
					$option=$opt_arr[$k]['option'];
					$o_id=$opt_arr[$k]['id'];
					$percent=$vote*100/$total;
					$y=$y-20;
					$p->set_text_pos( 120, $y);
					$p->show("option ".$o_id." is : ".$option);
				}
			$p->end_page_ext("");
			$p->begin_page_ext(11*72, 8.5*72, "");
				$font = $p->load_font("Times-Roman", "host", "");
			    $p->setfont( $font, 35);
			    $p->set_text_pos( 280, 560);
				$p->show('Time line statistics');
				$p->setfont( $font, 20);
				$p->set_text_pos( 150, 500);
				$p->show('Date Range');
				$p->set_text_pos( 350, 500);
				$p->show('Votes');
				$p->set_text_pos( 550, 500);
				$p->show('Percentage');
				$today=date('Y-m-d');// the dates
				$wb1=date('Y-m-d',strtotime('-7 days'));
				$wb2=date('Y-m-d',strtotime('-15 days'));
				$wb3=date('Y-m-d',strtotime('-21 days'));
				$mb=date('Y-m-d',strtotime('-1 months'));
				$mb3=date('Y-m-d',strtotime('-3 months'));
				$mb6=date('Y-m-d',strtotime('-6 months'));
				$yb=date('Y-m-d',strtotime('-1 years'));
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate >= '{$today}' ";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$todayVotes=count($res_arr);
				
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate > '$wb1' and voteDate <= '$today'";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$wbVotes=count($res_arr);
				
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate >'$wb2' and voteDate <= '$wb1'";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$wb2Votes=count($res_arr);
				
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate >'$wb3' and voteDate <= '$wb2'";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$wb3Votes=count($res_arr);
			
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate >'$mb' and voteDate <= '$wb3'";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$mbVotes=count($res_arr);
				
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate >'$mb3' and voteDate <= '$mb'";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$mb3Votes=count($res_arr);
				
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate >'$mb6' and voteDate <= '$mb3'";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$mb6Votes=count($res_arr);
				
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate >'$yb' and voteDate <= '$mb6'";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$ybVotes=count($res_arr);
				
				$query="SELECT `voteDate` FROM `votes` where q_id='{$id}' and voteDate <='$yb' ";
				$res=query($connect,$query);
				$res_arr=retrieve_data($res);
				$older=count($res_arr);
			
				$lastVote=$res_arr[0]['voteDate'];
				$query="SELECT `o_id`,`voteDate` FROM `votes` where id='{$id}'";
				$font = $p->load_font("Times-Roman", "host", "");
		   		$p->setfont($font, 14);
		   		$label=array('Today','1 Week Ago','2 Weeks Ago','3 Weeks Ago','1 Month Ago','3 Months Ago','6 Months Ago','1 Year Ago','Older Than 1 Year');
		   		$votes=array($todayVotes,$wbVotes,$wb2Votes,$wb3Votes,$mbVotes,$mb3Votes,$mb6Votes,$ybVotes,$older);
		   		$nbr=count($label);
				for($i=0;$i<$nbr;$i++)
				{
					$percent=$votes[$i]*100/$total;
					$y=470-$i*20;
					$p->set_text_pos( 160, $y);
					$p->show($label[$i]);
					$p->set_text_pos( 365, $y);
					$p->show($votes[$i]);
					$p->set_text_pos( 570, $y);
					$p->show(round($percent,2)." %");
				}
				$y=$y-40;
				
				$p->setfont( $font, 10);
				$p->set_text_pos( 120, $y);
				$p->show("Today Date is : ".date('F jS, Y'));
			$p->end_page_ext("");
		$p->end_document("");
		
	   $data = $p->get_buffer();
	   $len = strlen($data);
	
	   header("Content-type: application/pdf");
	   header("Content-Length: $len");
	   header("Content-Disposition: inline; filename=Poll Statistics".$id.".pdf");
	   print $data;
	   $p = 0;
	   close($connect);
		exit;
		}else if($res_nbr==0){echo "There is no statistics available for this poll at the moment !";close($connect);exit;}	
}else{echo "You aren't authorized to access this file !";}
?>